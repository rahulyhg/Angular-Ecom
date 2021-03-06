/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. "http://www.w3schools.com/angular/customers.php"
 */
/* global angular */

angular
    .module('frontEnd', [])
    .controller('moreCoveredEventsController', function ($scope, $http) {
        $scope.covered = "Covered ";
        $scope.events = "Events";
        $scope.btn_fb ="Facebook";
        $scope.btn_tw ="Tweet";
        $scope.btn_g ="Google+";
         $scope.btn_buy = "Buy Ticket";


         $scope.all = function(cID , status){
             if(status == 1){
                 moreCoverOnCat(cID);
             }else{
                 moreCover();
             }
         }
         
         $scope.all();
         $scope.floading = true;
          function moreCoverOnCat(categoryID){
             $http.post('php/more_covered_eventsPhpControllerOnCetagory.php' , {'cid': categoryID}).then(function(response) {
                 $scope.morecoveredEvents= response.data;
                 $scope.floading = false;
             });
         }
         
        
        function moreCover(){
             $http.get('php/more_covered_eventsPhpController.php').then(function(response) {
                 $scope.morecoveredEvents= response.data;
                 $scope.floading = false;
             });
         } 
         
         
            
         

        
      $http.post('php/navbarPhpController.php').then(function (response) {
                $scope.element = response.data;
                //$scope.menuElementEvent(response.data[0].category_id);
            });

        $scope.addToWishlist = function(Eid,Etype){
                if(Eid != ''){
                    $http.post('php/wishlistPhpController.php',{'id':Eid,'type':Etype}).then(function(response){
                        $scope.data = response.data;
                       //console.log(Eid,Etype)
                    });
                }
            }
    
    
     var minTime = 2;
             $scope.searchResult = '';
             $scope.EventHint = '';
             
             
             $scope.searchEvent = function(){
                 if($scope.EventHint.length == minTime )
                       $scope.executeSearchResult()
                 else  $scope.searchData = ' ';
             };
             
             $scope.executeSearchResult = function(){
                 $http.post("php/indexSearchPhpController.php").then(function(response){
                     $scope.searchResult = response.data;
                     
                 });
             }

    

    }).directive('checkImage', function ($http) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            attrs.$observe('ngSrc', function (ngSrc) {
                $http.get(ngSrc).success(function () {
//                    alert('image exist');
                }).error(function () {
                    // alert('image not exist');
                    element.attr('src', '/ticketchaiorg/upload/event_web_banner/defaultImg.jpg'); // set default image
                    element.attr('src', '/ticketchaiorg/upload/event_web_logo/defaultImg.jpg'); // set default image
                });
            });
        }
    };
});