app.controller('ReportesController', ['$scope', '$rootScope', '$modal', '$timeout', 'ngTableParams', '$filter', 'toaster', 'reportesService', '$window', function($scope, $rootScope, $modal, $timeout , ngTableParams, $filter, toaster, reportesService, $window) {
  
    $scope.dataVentas = [];
    $scope.tableVentas = [];
    $scope.asistencias = [];
    $scope.fechas = [];
    $scope.moduloTab = 1;
    $scope.moduloName = "Informe de ventas";
    $rootScope.pageTitle = "Reportes";
    var ruta = "http://190.151.129.244/asistencias/public/";
    $scope.settings = {
        singular: 'Reporte',
        plural: 'Reportes',
        modal: 'Reporte',
        accion: 'Guardar'
    }

    var fechas_ventas = { fecha_inicio: "", fecha_fin: ""};
    var fechas_asistencias = { fecha_inicio: "", fecha_fin: ""};

    $scope.$watch('filtro_ventas.fecha_inicio', function (newValue) {
        fechas_ventas.fecha_inicio = $filter('date')(newValue, 'yyyy/MM/dd'); 
    });
    $scope.$watch('filtro_ventas.fecha_fin', function (newValue) {
        fechas_ventas.fecha_fin = $filter('date')(newValue, 'yyyy/MM/dd'); 
    });
    $scope.$watch('filtro_asistencias.fecha_inicio', function (newValue) {
        fechas_asistencias.fecha_inicio = $filter('date')(newValue, 'yyyy/MM/dd'); 
    });
    $scope.$watch('filtro_asistencias.fecha_fin', function (newValue) {
        fechas_asistencias.fecha_fin = $filter('date')(newValue, 'yyyy/MM/dd'); 
    });

    $scope.selectedTab = function(n) {
        $scope.moduloTab = n;
    }    

    $scope.tableParamsVentas = new ngTableParams({
        page: 1,
        count: 10,
        sorting: {
            
        }
    }, {
        filterDelay: 50,
        total: $scope.tableVentas.length,
        getData: function($defer, params) {
            var searchStr = params.filter().search;
            if (searchStr) {

                searchStr = searchStr.toLowerCase();
                $scope.tableVentas = $scope.dataVentas.filter(function(item) {
                    return item.cemp.toLowerCase().indexOf(searchStr) > -1 || item.codigo_pdv.toLowerCase().indexOf(searchStr) > -1 || item.codigo_vendedor.toLowerCase().indexOf(searchStr) > -1 || item.tienda.toLowerCase().indexOf(searchStr) > -1;
                });

            } else {
                $scope.tableVentas = $scope.dataVentas;
            }
            $scope.tableVentas = params.sorting() ? $filter('orderBy')($scope.tableVentas, params.orderBy()) : $scope.tableVentas;
            $defer.resolve($scope.tableVentas.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
    });

    $scope.consultar_ventas = function()
    {
        reportesService.getVentas("GET", $scope.filtro_ventas).then( function(dataResponse){

            if(dataResponse.data.result)
            {
                $scope.dataVentas = dataResponse.data.records;
                $scope.tableVentas = dataResponse.data.records;
                $scope.tableParamsVentas.reload();
            }
        });
    }

    $scope.consultar_asistencias = function()
    {
        reportesService.getAsistencias("GET", $scope.filtro_asistencias).then( function(dataResponse){

            if(dataResponse.data.result)
            {
                $scope.fechas = dataResponse.data.fechas;
                $scope.asistencias = dataResponse.data.records;
            }
        });
    }

    $scope.exportar_ventas = function()
    {
        if(fechas_ventas.fecha_inicio!="" && fechas_ventas.fecha_fin!="")
            $window.open(ruta+'ws/exportar_ventas?fecha_inicio='+fechas_ventas.fecha_inicio+'&fecha_fin='+fechas_ventas.fecha_fin, '_blank');
        else
            $window.open(ruta+'ws/exportar_ventas', '_blank');
    }

    $scope.exportar_asistencias = function()
    {
        if(fechas_asistencias.fecha_inicio!="" && fechas_asistencias.fecha_fin!="")
            $window.open(ruta+'ws/exportar_asistencias?fecha_inicio='+fechas_asistencias.fecha_inicio+'&fecha_fin='+fechas_asistencias.fecha_fin, '_blank');
        else
            $window.open(ruta+'ws/exportar_asistencias', '_blank');
    }

}]);