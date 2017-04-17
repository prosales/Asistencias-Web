'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;
      }
    ]
  )
  .config(
    [          '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 
      function ($stateProvider,   $urlRouterProvider, JQ_CONFIG) {
          
          $urlRouterProvider
              .otherwise('/app/dashboard');
          $stateProvider
              .state('app.usuarios', {
                  url: '/usuarios',
                  templateUrl: 'tpl/usuarios.html',
                  controller: 'UsuariosController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/usuarios.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.supervisores', {
                  url: '/supervisores',
                  templateUrl: 'tpl/supervisores.html',
                  controller: 'SupervisoresController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/supervisores.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.vendedores', {
                  url: '/vendedores',
                  templateUrl: 'tpl/vendedores.html',
                  controller: 'VendedoresController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/vendedores.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.sucursales', {
                  url: '/sucursales',
                  templateUrl: 'tpl/sucursales.html',
                  controller: 'SucursalesController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/sucursales.js');
                              }
                          );
                      }]
                  }
              })
              .state('app', {
                  abstract: true,
                  url: '/app',
                  templateUrl: 'tpl/app.html'
              })
              .state('app.dashboard', {
                  url: '/dashboard',
                  templateUrl: 'tpl/dashboard.html',
                  controller: 'DashboardController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/dashboard.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.reportes', {
                  url: '/reportes',
                  templateUrl: 'tpl/reportes.html',
                  controller: 'ReportesController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/reportes.js');
                              }
                          );
                      }]
                  }
              })
      }
    ]
  );
