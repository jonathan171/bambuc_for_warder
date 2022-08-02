<?php

namespace App\Controller;

use App\Entity\CondicionPago;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\FacturaResolucion;
use App\Form\FacturaType;
use App\Repository\FacturaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura_simple')]
class FacturaSimpleController extends AbstractController
{
    #[Route('/', name: 'app_factura_simple_index', methods: ['GET'])]
    public function index(FacturaRepository $facturaRepository): Response
    {
        return $this->render('factura_simple/index.html.twig', [
            'facturas' => $facturaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_factura_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $factura = new Factura();
        $condicionPago = $entityManager
            ->getRepository(CondicionPago::class)
            ->find(77);
        $hora = new DateTime();
        $factura->setCondDePago($condicionPago);
        $factura->setTipoFactura('FACTURA_VENTA_SIMPLE');
        $factura->setHora($hora);


        $form = $this->createForm(FacturaType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numeroQuery = $entityManager->getRepository(Factura::class)->createQueryBuilder('c')
                ->andWhere('c.facturaResolucion = :val')
                ->setParameter('val', $factura->getFacturaResolucion()->getId())
                ->orderBy('c.numeroFactura', 'DESC')
                ->setMaxResults(1);

            $facturaResolucion = $entityManager->getRepository(FacturaResolucion::class)->find($factura->getFacturaResolucion()->getId());

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $factura->setNumeroFactura($consulta->getNumeroFactura() + 1);
            } else {
                $factura->setNumeroFactura($facturaResolucion->getInicioConsecutivo());
            }


            $entityManager->persist($factura);
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_simple_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('factura_simple/new.html.twig', [
            'factura' => $factura,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_factura_simple_show', methods: ['GET'])]
    public function show(Factura $factura): Response
    {
        return $this->render('factura_simple/show.html.twig', [
            'factura' => $factura,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_factura_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factura $factura, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FacturaType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $factura->setTotalReteIca($factura->getSubtotal() * ($factura->getReteIca() / 100));
            $factura->setTotalReteFuenteG($factura->getSubtotal() * ($factura->getReteFuente() / 100));
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_simple_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        return $this->renderForm('factura_simple/edit.html.twig', [
            'factura' => $factura,
            'form' => $form,
            'items' => $items,

        ]);
    }
    
    #[Route('/guardar_items', name: 'app_factura_simple_guardar_items', methods: ['GET', 'POST'])]
    public function executeGuardarItems(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        
            
           

            $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('factura_id'));
            $item = new FacturaItems();
            $item->setcodigo($request->request->get('codigo'));
            $item->setCantidad($request->request->get('cantidad'));
            $item->setDescripcion($request->request->get('descripcion'));
            $item->setValorUnitario($request->request->get('valor_unitario'));
            $item->setSubtotal($request->request->get('subtotal'));
            $item->setIva($request->request->get('iva'));
            $item->setValorIva($request->request->get('valor_iva'));
            $item->setTotal($request->request->get('total'));
            $item->setRetencionFuente($request->request->get('rete_fuente'));
            $item->setValorRetencionFuente($request->request->get('valor_rete_fuente'));
            $item->setTasaDescuento($request->request->get('descuento'));
            $item->setValorDescuento($request->request->get('valor_descuento'));
            $item->setFacturaClientes($factura);
            $entityManager->persist($item);
            $entityManager->flush();
            
        
       
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        $total_factura1 = 0;
        $totalIva = 0;
        $totalFuente = 0;
        $subtotal = 0;
        foreach ($items as $item) {
            $total_factura1 = $total_factura1 + $item->getTotal();
            $totalIva += $item->getValorIva();
            $totalFuente += $item->getValorRetencionFuente();
            $subtotal += $item->getSubtotal();
        }
        $factura->setTotal($total_factura1);
        $factura->setSubtotal($subtotal);
        $factura->setTotalIva($totalIva);
        $factura->setTotalReteIva($totalIva * ($factura->getReteIva() / 100));
        $factura->setTotalReteIca($subtotal * ($factura->getReteIca() / 100));
        $factura->setTotalReteFuenteG($subtotal * ($factura->getReteFuente() / 100));
        $factura->setTotalReteFuente($totalFuente);
        $entityManager->persist($factura);
        $entityManager->flush();

        return $this->redirectToRoute('app_factura_simple_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('item_delete/{id}', name: 'app_factura_simple_item_delete', methods: ['POST'])]
    public function itemDelete(Request $request, FacturaItems $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {

            $factura = $entityManager->getRepository(Factura::class)->find($item->getFacturaClientes()->getId());
            $factura->setSubtotal($factura->getSubtotal() - $item->getSubtotal());
            $factura->setTotal($factura->getTotal() - $item->getTotal());
            $factura->setTotalIva($factura->getTotalIva() - $item->getValorIva());
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $factura->setTotalReteIca(($factura->getReteIca() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuenteG(($factura->getReteFuente() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuente($factura->getTotalReteFuente() - $item->getValorRetencionFuente());

            $entityManager->persist($factura);
            $entityManager->flush();

            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_simple_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_factura_simple_delete', methods: ['POST'])]
    public function delete(Request $request, Factura $factura, FacturaRepository $facturaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$factura->getId(), $request->request->get('_token'))) {
            $facturaRepository->remove($factura, true);
        }

        return $this->redirectToRoute('app_factura_simple_index', [], Response::HTTP_SEE_OTHER);
    }
}
