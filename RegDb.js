(function(angular) {
    'use strict';

    var myApp = angular.module('EventRegApp',['ui.bootstrap', 'ngCookies']);

    myApp.controller('RegController', function ($http, $uibModal, $log, $filter, $cookies)
    {
    	var $ctrl = this;

		this.ShowReg = function(key)
		{
			this.userMsg = "Show:" + key;
			this.detail = this.registration[key];
			$('.nav-tabs a[href="#home"]').tab('show')
		}

		this.Search = function()
		{
			this.userMsg = "Starting Search";
			this.detail = { regID : 0 };
			var scope = this;
			$http.post("GetRegData.php", this.searchData)
				.success(function(response)
				{
					scope.registration = response.registration;
					scope.userMsg = "Data Loaded";
				})
				.error(function(response)
					{
						scope.userMsg = "Things did not go well";

					}
				)
				;

		}

		this.OpenPayment = function(size)
		{
			var modalInstance = $uibModal.open({
				animation: this.animationsEnabled,
				ariaLabelledBy: 'modal-title',
				ariaDescribedBy: 'modal-body',
				templateUrl: 'AddPaymentDialog.html',
				controller: 'AddPaymentDialogCtrl',
				controllerAs: '$ctrl',
				size: size,
				resolve:
				{
					NoteText : function ()
					{
						return $ctrl.NoteText;
					}
				}
			});

			// This will be called when the user select 'Ok'
			modalInstance.result.then(function (noteText)
			{
				$log.info("NoteText: " + noteText);
				$ctrl.NoteText = noteText;
				if ( !$ctrl.detail.Notes ) $ctrl.detail.Notes = '';
				$ctrl.detail.Notes = $filter('date')(new Date(),'yyyy-MM-dd HH:mm') + ':' + noteText + "\n" + $ctrl.detail.Notes;
			}, function() {
				$log.info("Model Dismissed");
			})
		}

		this.OpenNote = function(size)
		{
			var modalInstance = $uibModal.open({
				animation: this.animationsEnabled,
				ariaLabelledBy: 'modal-title',
				ariaDescribedBy: 'modal-body',
				templateUrl: 'AddNoteDialog.html',
				controller: 'AddNoteDialogCtrl',
				controllerAs: '$ctrl',
				size: size,
				resolve:
				{
					NoteText : function ()
					{
						return $ctrl.NoteText;
					}
				}
			});

			// This will be called when the user select 'Ok'
			modalInstance.result.then(function (noteText)
			{
				$log.info("NoteText: " + noteText);
				$ctrl.NoteText = noteText;
				if ( !$ctrl.detail.Notes ) $ctrl.detail.Notes = '';
				$ctrl.detail.Notes = $filter('date')(new Date(),'yyyy-MM-dd HH:mm') + ':' + noteText + "\n" + $ctrl.detail.Notes;
			}, function() {
				$log.info("Model Dismissed");
			})
		}

    	this.LoadAll = function()
		{
            this.userMsg = "Starting Reload:" +  $filter('date')(new Date(),'yyyy-MM-dd HH:mm:ss Z');
            var scope = this;
			$http.get("GetRegData.php")
				.then(function(response)
				{
					scope.registration = response.data.registration;
					scope.userMsg = "Data Loaded";
				});
		}

		this.GetStartupData = function()
		{
			this.userMsg = "GetStartupData:" +  $filter('date')(new Date(),'yyyy-MM-dd HH:mm:ss Z');
			var scope = this;
			$http.get("GetStartupData.php")
				.then(function(response)
				{
					scope.events = response.data.events;
					scope.config = response.data.config;
					scope.userMsg = "Startup Loaded";
				});
		}

		this.GetSummary = function()
		{
			this.userMsg = "GetSummary:" +  $filter('date')(new Date(),'yyyy-MM-dd HH:mm:ss Z');
			var scope = this;
			$http.get("GetEventSummary.php?event=" + this.searchData.event)
				.then(function(response)
				{
					scope.eventSummary = response.data;
					scope.userMsg = "Summary Loaded";
				});
		}

		this.SetEvent = function()
		{
			$cookies.put('event', this.searchData.event);
		}



		this.config = {};
		this.detail = { regID : 0 };
		this.searchData = {};
		this.searchData.firstName = "Ste";
		this.searchData.event = $cookies.get('event');
		this.showModel = false;

		this.NoteText = "This is note text";

		this.GetStartupData();
		$('.nav-tabs a[href="#filter"]').tab('show')
	});

	myApp.controller('AddNoteDialogCtrl', function ($uibModalInstance, NoteText)
	{
		var $ctrl = this;
		$ctrl.NoteText = NoteText;

		$ctrl.ok = function () {
			$uibModalInstance.close($ctrl.NoteText);
		};

		$ctrl.cancel = function () {
			$uibModalInstance.dismiss('cancel');
		};
	});

	myApp.controller('AddPaymentDialogCtrl', function ($uibModalInstance, NoteText)
	{
		var $ctrl = this;

		$ctrl.ok = function () {
			$uibModalInstance.close($ctrl.NoteText);
		};

		$ctrl.cancel = function () {
			$uibModalInstance.dismiss('cancel');
		};
	});

})(window.angular);
