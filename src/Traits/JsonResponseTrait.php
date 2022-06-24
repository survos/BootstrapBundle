<?php


namespace Survos\BaseBundle\Traits;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait JsonResponseTrait
{

    public function jsonResponse($data, Request $request = null, $format='html')
    {
        if ($request && $request->isXmlHttpRequest()) {
            $format = 'json';
        }
        return $format === 'json'
            ? new JsonResponse($data)
            : new Response(sprintf('<html lang="en"><body><pre>%s</pre></body></html>', json_encode($data, JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT )) );
    }
}