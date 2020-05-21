(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.powerBI = {
    attach: function(context, settings) {
      if(settings.powerBI) {
        var models = window['powerbi-client'].models;
        var permissions = models.Permissions.All;
        var config = {
            type: 'report',
            tokenType: models.TokenType.Embed,
            accessToken: settings.embedToken,
            embedUrl: settings.embedUrl,
            id: settings.reportId,
            permissions: permissions,
            settings: {
                filterPaneEnabled: true,
                navContentPaneEnabled: true
            }
        };
        console.log(config);
        var $reportContainer = $('#emdedReportPowerBI');
        var embedContainer = $reportContainer.get(0);
        var report = powerbi.embed(embedContainer, config);

        report.off("loaded");

        report.on("loaded", function () {
            console.log("Loaded");
        });

        report.off("rendered");

        report.on("rendered", function () {
            console.log("Rendered");
        });

        report.on("error", function (event) {
            console.log(event.detail);
            report.off("error");
        });

        report.off("saved");
        report.on("saved", function (event) {
            console.log(event.detail);
            if (event.detail.saveAs) {
                console.log('In order to interact with the new report, create a new token and load the new report');
            }
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings);

