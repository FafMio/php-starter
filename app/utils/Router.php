<?php

class Router {
    protected AltoRouter $router;

    /**
     * @throws Exception
     */
    public function __construct() {
        $this->router = new AltoRouter();

        //* Page d'accueil du site
        $this->router->map(
            'GET',
            '/',
            [
                'controller' => 'MainController',
                'method' => 'home',
            ],
            'main-home'
        );

        //? Generate Page
        $this->router->map(
            'GET|POST',
            '/generate-movie/[:id]',
            [
                'controller' => 'MainController',
                'method' => 'generate_movie',
            ],
            'main-generate-movie'
        );
    }

   
    /**
     * Get the value of router
     */ 
    public function get(): AltoRouter
    { return $this->router; }

    /**
     * Set the value of router
     *
     * @param $router
     * @return  self
     */
    public function set($router): static
    {
        $this->router = $router;
        return $this;
    }
}
