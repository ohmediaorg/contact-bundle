<?php

namespace OHMedia\ContactBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\ContactBundle\Entity\Location;
use OHMedia\ContactBundle\Form\LocationType;
use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\ContactBundle\Security\Voter\LocationVoter;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class LocationController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'location_reorder';

    #[Route('/locations', name: 'location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        $newLocation = new Location();

        $this->denyAccessUnlessGranted(
            LocationVoter::INDEX,
            $newLocation,
            'You cannot access the list of locations.'
        );

        $locations = $locationRepository->findAllOrdered();

        return $this->render('@OHMediaContact/location/location_index.html.twig', [
            'locations' => $locations,
            'new_location' => $newLocation,
            'attributes' => $this->getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/locations/reorder', name: 'location_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        LocationRepository $locationRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::INDEX,
            new Location(),
            'You cannot reorder the locations.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $locations = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($locations as $ordinal => $id) {
                $location = $locationRepository->find($id);

                if ($location) {
                    $location->setOrdinal($ordinal);

                    $locationRepository->save($location, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/location/create', name: 'location_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        LocationRepository $locationRepository
    ): Response {
        $location = new Location();

        $this->denyAccessUnlessGranted(
            LocationVoter::CREATE,
            $location,
            'You cannot create a new location.'
        );

        $form = $this->createForm(LocationType::class, $location);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($location, $locationRepository);

            $this->addFlash('notice', 'The location was created successfully.');

            return $this->redirectToRoute('location_index');
        }

        return $this->render('@OHMediaContact/location/location_create.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    #[Route('/location/{id}/edit', name: 'location_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Location $location,
        LocationRepository $locationRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::EDIT,
            $location,
            'You cannot edit this location.'
        );

        $form = $this->createForm(LocationType::class, $location);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($location, $locationRepository);

            $this->addFlash('notice', 'The location was updated successfully.');

            return $this->redirectToRoute('location_index');
        }

        return $this->render('@OHMediaContact/location/location_edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    private function save(
        Location $location,
        LocationRepository $locationRepository
    ): void {
        foreach ($location->getHours() as $hours) {
            $hours->setLocation($location);
        }

        if ($location->isPrimary()) {
            $primary = $locationRepository->findPrimary();

            if ($primary && $primary !== $location) {
                $primary->setPrimary(false);

                $locationRepository->save($primary, true);
            }
        }

        $locationRepository->save($location, true);
    }

    #[Route('/location/{id}/delete', name: 'location_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Location $location,
        LocationRepository $locationRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            LocationVoter::DELETE,
            $location,
            'You cannot delete this location.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locationRepository->remove($location, true);

            $this->addFlash('notice', 'The location was deleted successfully.');

            return $this->redirectToRoute('location_index');
        }

        return $this->render('@OHMediaContact/location/location_delete.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => LocationVoter::CREATE,
            'delete' => LocationVoter::DELETE,
            'edit' => LocationVoter::EDIT,
        ];
    }
}
