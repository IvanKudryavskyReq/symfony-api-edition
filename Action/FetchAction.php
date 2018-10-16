<?php

namespace Requestum\ApiBundle\Action;

use Requestum\ApiBundle\Action\Extension\SubResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FetchAction.
 */
class FetchAction extends EntityAction implements SubResourceInterface
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function executeAction(Request $request)
    {
        $expandExpression = $request->query->get('expand') ? $request->query->get('expand') : null;
        $expand = $expandExpression ? explode(',', $expandExpression) : [];

        $user = $this->getEntity($request, $this->options['fetch_field']);
        $this->checkAccess($user);

        return $this->handleResponse(
            $user,
            Response::HTTP_OK,
            ['expand' => $expand]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'access_attribute' => 'fetch',
        ]);
    }
}
