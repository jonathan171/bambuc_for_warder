<?php

namespace App\Controller;

use App\Entity\CondicionPago;
use App\Entity\EnviosNacionales;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\FacturaResolucion;
use App\Entity\UnidadesMedida;
use App\Form\FacturaType;
use App\Repository\FacturaRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura_nacionales')]
class FacturaNacionalesController extends AbstractController
{
    #[Route('/', name: 'app_factura_nacionales_index', methods: ['GET'])]
    public function index(FacturaRepository $facturaRepository): Response
    {
        return $this->render('factura_nacionales/index.html.twig', [
            'facturas' => $facturaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_factura_nacionales_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $factura = new Factura();
        $condicionPago = $entityManager
            ->getRepository(CondicionPago::class)
            ->find(77);
        $hora = new DateTime();
        $factura->setCondDePago($condicionPago);
        $factura->setTipoFactura('FACTURA_VENTA_NACIONAL');
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

            return $this->redirectToRoute('app_factura_nacionales_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('factura_nacionales/new.html.twig', [
            'factura' => $factura,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_factura_nacionales_show', methods: ['GET'])]
    public function show(Factura $factura): Response
    {
        return $this->render('factura_nacionales/show.html.twig', [
            'factura' => $factura,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_factura_nacionales_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factura $factura, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FacturaType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $factura->setTotalReteIca($factura->getSubtotal() * ($factura->getReteIca() / 100));
            $factura->setTotalReteFuenteG($factura->getSubtotal() * ($factura->getReteFuente() / 100));
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_nacionales_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        return $this->renderForm('factura_nacionales/edit.html.twig', [
            'factura' => $factura,
            'form' => $form,
            'items' => $items,

        ]);
    }
    
    //facturar todos los evios que han sido seleccionados

    #[Route('/facturar_envios', name: 'app_factura_nacionales_facturar_envios', methods: ['GET', 'POST'])]
    public function executeFacturarEnvios(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $enviosId = (array) $request->request->get('envId');
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('factura_id'));
        $unidad =  $entityManager->getRepository(UnidadesMedida::class)->find(67);

        foreach ($enviosId as $envioId) {

            $envio = $entityManager->getRepository(EnviosNacionales::class)->find($envioId);

            $item = new FacturaItems();
            $item->setCantidad($envio->getUnidades());
            $item->setDescripcion('TRANSPORTE NACIONAL');
            $item->setValorUnitario($envio->getValorTotal()/$envio->getUnidades());
            $item->setSubtotal($envio->getValorTotal());
            $item->setIva(0);
            $item->setValorIva(0);
            $item->setTotal($envio->getValorTotal());
            $item->setFacturaClientes($factura);
            $item->setCodigo($envio->getNumero());
            $item->setRetencionFuente(0);
            $item->setValorRetencionFuente(0);
            $item->setTasaDescuento(0);
            $item->setValorDescuento(0);
            $item->setUnidad($unidad);

            $entityManager->persist($item);
            $entityManager->flush();

            $envio->setFacturado(1);
            $envio->setFacturaItems($item);

            $entityManager->persist($envio);
            $entityManager->flush();

            $factura->setSubtotal($factura->getSubtotal() + $item->getSubtotal());
            $factura->setTotal($factura->getTotal() + $item->getTotal());
            $factura->setTotalIva($factura->getTotalIva() +  $item->getValorIva());
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $factura->setTotalReteIca(($factura->getReteIca() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuenteG(($factura->getReteFuente() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuente($factura->getTotalReteFuente() + $item->getValorRetencionFuente());


            $entityManager->persist($factura);
            $entityManager->flush();
        }
   
          
        return $this->redirectToRoute('app_factura_nacionales_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        
        
    }

    #[Route('/cargar_items', name: 'app_factura_nacionales_cargar_items', methods: ['GET', 'POST'])]
    public function executeCargarItems(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $datos = json_decode($request->request->get('datos'));
        $total = 0;

        foreach ($datos as $dato) {
            if ($dato != null && $dato != '') {

                $total = $dato[4]->valor;
                if ($dato[1]->valor != '' && $dato[1]->valor != null) {
                    $valorDescuento = ($dato[1]->valor / 100) * $dato[5]->valor;
                    $dato[5]->valor -= $valorDescuento;
                    $total = $dato[5]->valor;
                } else {
                    $valorDescuento = 0;
                }

                if ($dato[3]->valor != '' && $dato[3]->valor != null) {
                    $valorIva = (($dato[3]->valor / 100)) * $dato[5]->valor;
                    $total = (($dato[3]->valor / 100) + 1) * $dato[5]->valor;
                } else {
                    $dato[3]->valor = 0;
                    $valorIva = 0;
                }

                if ($dato[2]->valor != '' && $dato[2]->valor != null) {
                    $valorReFue = ($dato[2]->valor / 100) * $dato[5]->valor;
                    $total -= $valorReFue;
                } else {
                    $valorReFue = 0;
                }

                $item = $entityManager->getRepository(FacturaItems::class)->find($request->request->get('id'));

                $item->setDescripcion($dato[0]->valor);
                $item->setSubtotal($dato[5]->valor);
                $item->setIva($dato[3]->valor);
                $item->setValorIva($valorIva);
                $item->setTotal($total);
                $item->setRetencionFuente($dato[2]->valor);
                $item->setValorRetencionFuente($valorReFue);
                $item->setTasaDescuento($dato[1]->valor);
                $item->setValorDescuento($valorDescuento);
                $entityManager->persist($item);
                $entityManager->flush();
            }
        }
        $factura = $item->getFacturaClientes();
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


        $thearray[0] = number_format($total, 2, '.', '');
        $thearray[1] = $request->request->get('id');
        $thearray[2] = number_format($total_factura1 - ($factura->getTotalReteIca() + $factura->getTotalReteIva()), 2, '.', '');


        return $this->json($thearray);
    }
    #[Route('item_delete/{id}', name: 'app_factura_nacionales_item_delete', methods: ['POST'])]
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


            $envio = $entityManager->getRepository(EnviosNacionales::class)->findOneBy(['facturaItems' => $item->getId()]);
            $envio->setFacturado(0);
            $envio->setFacturaItems(NULL);
            $entityManager->persist($envio);
            $entityManager->flush();


            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_nacionales_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_factura_nacionales_delete', methods: ['POST'])]
    public function delete(Request $request, Factura $factura, FacturaRepository $facturaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$factura->getId(), $request->request->get('_token'))) {
            $facturaRepository->remove($factura, true);
        }

        return $this->redirectToRoute('app_factura_nacionales_index', [], Response::HTTP_SEE_OTHER);
    }
}
