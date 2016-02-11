<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  $routes->get('/login', function() {
    HelloWorldController::login();
  });


  $routes->get('/items/1', function() {
    HelloWorldController::item_show();
  });

  $routes->get('/items/1/edit', function() {
    HelloWorldController::item_edit();
  });

  // Vaatteen lisäyssivu
//Pitääköhän tämän olla nimenomaan ylempänä esittelysivua?
  $routes->get('/items/new', function() {
    ItemController::create();
  });

  $routes->get('/people', function() {
    PersonController::index();
  });

  // Vaatteiden indeksisivu
  $routes->get('/items', function() {
    ItemController::index();
  });
  // Vaatteen esittelysivu
  $routes->get('/items/:item_id', function($item_id){
    ItemController::show($item_id);
  });
  




