app.controller('VendedoresController', ['$scope', '$rootScope', '$modal', '$timeout', 'vendedoresService', 'sucursalesService', 'supervisoresService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , vendedoresService, sucursalesService, supervisoresService, ngTableParams, $filter, toaster) {
  
    $scope.data = [];
    $scope.table = [];
    $scope.sucursales = [];
    $scope.supervisores = [];
    $rootScope.pageTitle = "Vendedores";
    $scope.settings = {
        singular: 'Vendedor',
        plural: 'Vendedores',
        modal: 'Crear Vendedor',
        accion: 'Guardar'
    }

    function actualizar_datos()
    {
        vendedoresService.getData('GET', {}).then(function(dataResponse) {
                $scope.data = dataResponse.data.records;
                $scope.table = dataResponse.data.records;
                $scope.tableParams.reload();
        });

        sucursalesService.getData('GET', {}).then(function(dataResponse) {
                $scope.sucursales = dataResponse.data.records;
        });

        supervisoresService.getData('GET', {}).then(function(dataResponse) {
                $scope.supervisores = dataResponse.data.records;
        });
    }

    actualizar_datos();

    $scope.tableParams = new ngTableParams({
        page: 1,
        count: 10,
        sorting: {
            id: 'asc'
        }
    }, {
        filterDelay: 50,
        total: $scope.table.length,
        getData: function($defer, params) {
            var searchStr = params.filter().search;
            if (searchStr) {

                searchStr = searchStr.toLowerCase();
                $scope.table = $scope.data.filter(function(item) {
                    return item.nombre.toLowerCase().indexOf(searchStr) > -1 || item.codigo.toLowerCase().indexOf(searchStr) > -1 || item.sucursal.codigo.toLowerCase().indexOf(searchStr) > -1 || item.sucursal.cadena.toLowerCase().indexOf(searchStr) > -1 || item.sucursal.tienda.toLowerCase().indexOf(searchStr) > -1 || item.sucursal.region.toLowerCase().indexOf(searchStr) > -1 || item.sucursal.supervisor.toLowerCase().indexOf(searchStr) > -1;
                });

            } else {
                $scope.table = $scope.data;
            }
            $scope.table = params.sorting() ? $filter('orderBy')($scope.table, params.orderBy()) : $scope.table;
            $defer.resolve($scope.table.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
    });

    var modalInstance;

    $scope.createItem = function()
    {
        $scope.item = {};
        $scope.settings.accion = "Crear";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-vendedor.html',
            scope: $scope,
            size: 'lg'
      });
    }

    $scope.editItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Editar Puesto";
        $scope.settings.accion = "Editar";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-vendedor.html',
            scope: $scope,
            size: 'lg'
        })
    }

    $scope.addPhone = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Agregar Telefonos";
        $scope.settings.accion = "Agregar";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-telefonos.html',
            scope: $scope,
            size: 'lg'
        })
    }

    $scope.deleteItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Eliminar Puesto";
        $scope.settings.accion = "Eliminar";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-eliminar.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.bajaItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Baja Puesto";
        $scope.settings.accion = "Dar baja";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-baja.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.altaItem = function(item)
    {
        $scope.item = item;
        $scope.settings.modal = "Alta Puesto";
        $scope.settings.accion = "Dar alta";
        modalInstance = $modal.open({
            templateUrl: 'tpl/partials/modal-alta.html',
            scope: $scope,
            size: 'lg'
        });
    }

    $scope.hide = function()
    {
        modalInstance.close();
    }

    $scope.saveItem = function()
    {
        if( $scope.settings.accion == "Crear" )
        {
            vendedoresService.create( $scope.item ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Editar" )
        {
            vendedoresService.update( $scope.item ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Eliminar" )
        {
            vendedoresService.delete( $scope.item.id ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Dar alta" )
        {
            var data = {
                id: $scope.item.id,
                estado: 1
            }
            vendedoresService.update( data ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
        else if( $scope.settings.accion == "Dar baja" )
        {
            var data = {
                id: $scope.item.id,
                estado: 0
            }
            vendedoresService.update( data ).then(function(dataResponse){

                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    modalInstance.close();
                    actualizar_datos();
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            })
        }
    }

    $scope.savePhone = function(event)
    {
        vendedoresService.createTelefono( $scope.item ).then(function(dataResponse){

            if(dataResponse.data.result)
            {
                toaster.pop('success', 'Exito!', dataResponse.data.message);
                $scope.item.telefonos.push( dataResponse.data.records );
            }
            else
            {
                toaster.pop('Error', 'Espera!', dataResponse.data.message);
            }
        });
    }

}]);