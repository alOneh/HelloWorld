<?php
require_once 'silex.phar';

use Silex\Application;

use Silex\Extension\TwigExtension;
use Silex\Extension\FormExtension;
use Silex\Extension\SymfonyBridgesExtension;
use Silex\Extension\TranslationExtension;
use Symfony\Component\Form;

$app = new Application();

// Register all services

// Provide some required Twig/Extension to deal with FormExtension
$app->register(new SymfonyBridgesExtension(), array(
    'symfony_bridges.class_path' => __DIR__ . '/vendors/symfony'
));

// Register it because the TwigFormExtension
$app->register(new TranslationExtension(), array(
    'translation.class_path' => __DIR__ . '/vendors/symfony',
    'translator.messages' => array()
));

$app->register(new TwigExtension(), array(
    'twig.path' => __DIR__ . '/views',
    'twig.class_path' => __DIR__ . '/vendors/twig/lib'
));

// You have to copy Symfony/Component/Form in your vendors/symfony until they update the symfony/Form subtree split on github
$app->register(new FormExtension(), array(
    'form.class_path' => __DIR__ . '/vendors/symfony'
));

// Use match() method to handle the form's submit on this route (GET & POST method)
$app->match('/', function() use ($app) {

    $form = $app['form.factory']->createBuilder('form')
        ->add('name', 'text')
        ->add('email', 'text')
        ->add('message', 'textarea')
    ->getForm();

    $request = $app['request'];

    if ($request->getMethod() == 'POST')
    {
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $data = $form->getData();

            return $app['twig']->render('index.twig', array('data' => $data));
        }

    }

    return $app['twig']->render('form.twig', array('form' => $form->createView()));

}, "GET|POST");

$app->run();