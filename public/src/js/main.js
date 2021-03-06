'use strict';

/* Controllers */

angular.module('app')
  .controller('AppCtrl', ['$scope', '$translate', '$localStorage', '$window', 'localStorageService', '$modal', '$http', 'APP',
    function(              $scope,   $translate,   $localStorage,   $window, localStorageService, $modal, $http, APP ) {

      if (!localStorageService.cookie.get('login')) {
            $window.location.href = 'login.html';
      }
      
      $scope.login = {
        nombre: localStorageService.cookie.get('login').nombre,
        idusuario: localStorageService.cookie.get('login').id,
        tipo: localStorageService.cookie.get('login').tipo
      }

      console.log($scope.login);

      // add 'ie' classes to html
      var isIE = !!navigator.userAgent.match(/MSIE/i);
      isIE && angular.element($window.document.body).addClass('ie');
      isSmartDevice( $window ) && angular.element($window.document.body).addClass('smart');

      $scope.nombre = localStorageService.cookie.get('login').nombre;
      $scope.usuario = localStorageService.cookie.get('login').usuario;
      $scope.idusuario = localStorageService.cookie.get('login').id;
      $scope.idrol = localStorageService.cookie.get('login').idrol;
      $scope.idpuesto = localStorageService.cookie.get('login').idpuesto;

      var modalInstance;

      $scope.modalPassword = function()
      {
          modalInstance = $modal.open({
              templateUrl: 'tpl/partials/modal-password.html',
              scope: $scope,
              size: 'lg'
        });
      }

      $scope.cerrarModal = function()
      {
          modalInstance.close();
      }

      $scope.generarPassword = function()
      {
          $http.get(APP.api + 'generar_password').then(function(dataResponse){

              if(dataResponse.data.result)
              {
                $scope.generar = { password : dataResponse.data.records.password};
              }
              else
              {
                alert(dataResponse.data.message);
              }

          });
      }

      // config
      $scope.app = {
        name: 'Asistencias',
        version: '1.0',
        // for chart colors
        color: {
          primary: '#7266ba',
          info:    '#23b7e5',
          success: '#27c24c',
          warning: '#fad733',
          danger:  '#f05050',
          light:   '#e8eff0',
          dark:    '#3a3f51',
          black:   '#1c2b36'
        },
        settings: {
          themeID: 1,
          navbarHeaderColor: 'bg-black',
          navbarCollapseColor: 'bg-white-only',
          asideColor: 'bg-black',
          headerFixed: true,
          asideFixed: false,
          asideFolded: false,
          asideDock: false,
          container: false
        }
      }

      // save settings to local storage
      if ( angular.isDefined($localStorage.settings) ) {
        $scope.app.settings = $localStorage.settings;
      } else {
        $localStorage.settings = $scope.app.settings;
      }
      $scope.$watch('app.settings', function(){
        if( $scope.app.settings.asideDock  &&  $scope.app.settings.asideFixed ){
          // aside dock and fixed must set the header fixed.
          $scope.app.settings.headerFixed = true;
        }
        // save to local storage
        $localStorage.settings = $scope.app.settings;
      }, true);

      // angular translate
      $scope.lang = { isopen: false };
      $scope.langs = {en:'English', de_DE:'German', it_IT:'Italian'};
      $scope.selectLang = $scope.langs[$translate.proposedLanguage()] || "English";
      $scope.setLang = function(langKey, $event) {
        // set the current lang
        $scope.selectLang = $scope.langs[langKey];
        // You can change the language during runtime
        $translate.use(langKey);
        $scope.lang.isopen = !$scope.lang.isopen;
      };

      function isSmartDevice( $window )
      {
          // Adapted from http://www.detectmobilebrowsers.com
          var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
          // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
          return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
      }

  }]);
