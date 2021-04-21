<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/hello/{name}", name="homepage")
     */
    // public function index(): Response
    // public function index(Request $request): Response
    public function index(string $name = ''): Response
    {
        // return $this->render('conference/index.html.twig', [
        //     'controller_name' => 'ConferenceController',
        // ]);
        $greet = '';
        // if ($name = $request->query->get('hello')) {
        if ($name ) {
            $greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
        }
                return new Response(<<<EOF
                    <html>
                        <body>
                            $greet
                            <img src="/images/under-construction.gif" />
                        </body>
                    </html>
                    EOF
                );
    }
}
