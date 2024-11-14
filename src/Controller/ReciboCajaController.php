<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\EnviosNacionales;
use App\Entity\Municipio;
use App\Entity\ReciboCaja;
use App\Entity\ReciboCajaItem;
use App\Entity\UnidadesMedida;
use App\Entity\User;
use App\Form\ReciboCajaType;
use App\Repository\ReciboCajaRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recibo_caja')]
class ReciboCajaController extends AbstractController
{
    #[Route('/', name: 'app_recibo_caja_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('recibo_caja/index.html.twig', [
           
        ]);
    }

    #[Route('/new', name: 'app_recibo_caja_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReciboCajaRepository $reciboCajaRepository, EntityManagerInterface $entityManager): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reciboCaja = new ReciboCaja();
        $ultimoNumero = $reciboCajaRepository->findLastNumeroRecibo() ? $reciboCajaRepository->findLastNumeroRecibo(): 0;
        
        $reciboCaja->setNumeroRecibo( $ultimoNumero+1);
        $municipio = $entityManager->getRepository(Municipio::class)->find('2');
        $pagaA = $entityManager->getRepository(User::class)->find('8');
        $reciboCaja->setMunicipio($municipio);
        $reciboCaja->setCreadaPor($this->getUser());
        $reciboCaja->setPagadaA($pagaA);
        $reciboCaja->setSubTotal(0);
        $reciboCaja->setTotal(0);

        $form = $this->createForm(ReciboCajaType::class, $reciboCaja);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reciboCajaRepository->add($reciboCaja, true);

            return $this->redirectToRoute('app_recibo_caja_edit', ["id"=>$reciboCaja->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recibo_caja/new.html.twig', [
            'recibo_caja' => $reciboCaja,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_recibo_caja_show', methods: ['GET'])]
    public function show(ReciboCaja $reciboCaja): Response
    {
        return $this->render('recibo_caja/show.html.twig', [
            'recibo_caja' => $reciboCaja,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recibo_caja_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReciboCaja $reciboCaja, ReciboCajaRepository $reciboCajaRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReciboCajaType::class, $reciboCaja);
        $form->handleRequest($request);

        $items = $entityManager->getRepository(ReciboCajaItem::class)->createQueryBuilder('ri')
        ->andWhere('ri.reciboCaja = :val')
        ->setParameter('val', $reciboCaja->getId())
        ->getQuery()->getResult();
        if ($form->isSubmitted() && $form->isValid()) {
            $reciboCajaRepository->add($reciboCaja, true);

            return $this->redirectToRoute('app_recibo_caja_edit', ["id"=>$reciboCaja->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recibo_caja/edit.html.twig', [
            'recibo_caja' => $reciboCaja,
            'form' => $form,
            'items' => $items
        ]);
    }

    #[Route('/{id}/delete', name: 'app_recibo_caja_delete', methods: ['POST'])]
    public function delete(Request $request, ReciboCaja $reciboCaja, ReciboCajaRepository $reciboCajaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reciboCaja->getId(), $request->request->get('_token'))) {
            $reciboCajaRepository->remove($reciboCaja, true);
        }

        return $this->redirectToRoute('app_recibo_caja_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('item_delete/{id}', name: 'app_recibo_caja_item_delete', methods: ['POST'])]
    public function itemDelete(Request $request, ReciboCajaItem $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {

            $recibo = $entityManager->getRepository(ReciboCaja::class)->find($item->getReciboCaja()->getId());
            $recibo->setSubtotal($recibo->getSubtotal() - $item->getSubtotal());
            $recibo->setTotal($recibo->getTotal() - $item->getTotal());

            $entityManager->persist($recibo);
            $entityManager->flush();


            $envio = $entityManager->getRepository(EnviosNacionales::class)->findOneBy(['reciboItems' => $item->getId()]);
            if(!$envio){
                $envio = $entityManager->getRepository(Envio::class)->findOneBy(['reciboItems' => $item->getId()]); 
            }

            $envio->setFacturadoRecibo(0);
            $envio->setReciboItems(NULL);

            $entityManager->persist($envio);
            $entityManager->flush();


            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recibo_caja_edit', ['id' => $recibo->getId()], Response::HTTP_SEE_OTHER);
    }
   

    #[Route('/facturar_envios', name: 'app_recibo_caja_facturar_envios', methods: ['GET', 'POST'])]
    public function executeFacturarEnvios(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $enviosId = (array) $request->request->get('envId');
        $recibo = $entityManager->getRepository(ReciboCaja::class)->find($request->request->get('recibo'));
        
        foreach ($enviosId as $envioId) {

            $envio = $entityManager->getRepository(EnviosNacionales::class)->find($envioId);

            $item = new ReciboCajaItem();
            $item->setCantidad($envio->getUnidades());
            $item->setDescripcion('TRANSPORTE ENVIO');
            $item->setValorUnitario($envio->getValorTotal()/$envio->getUnidades());
            $item->setSubtotal($envio->getValorTotal());
            $item->setTotal($envio->getValorTotal());
            $item->setReciboCaja($recibo);
            $item->setCodigo($envio->getNumero());

            $entityManager->persist($item);
            $entityManager->flush();

            $envio->setFacturadoRecibo(1);
            $envio->setReciboItems($item);

            $entityManager->persist($envio);
            $entityManager->flush();

            $recibo->setSubTotal($recibo->getSubTotal() + $item->getSubTotal());
            $recibo->setTotal($recibo->getTotal() + $item->getTotal());
            


            $entityManager->persist( $recibo);
            $entityManager->flush();
        }
   
          
        return $this->redirectToRoute('app_recibo_caja_edit', ['id' => $recibo->getId()], Response::HTTP_SEE_OTHER);
        
        
    }
    #[Route('/table', name: 'app_recibo_caja_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, ReciboCajaRepository $reciboRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');
        $order = $request->request->get('order');
        $nacional = $request->query->get('nacional');



        $data_table  = $reciboRepository->findByDataTable(['page' => ($start / $length), 'pageSize' => $length, 'search' => $search['value'], 'order' => $order, 'nacional'=> $nacional, 'company'=>   $request->query->get('company')]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }

}
