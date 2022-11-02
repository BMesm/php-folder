<?php
// This code should be executed server-side. Your API key and token should be kept confidential.
require 'vendor/autoload.php';
use Cumulio\Cumulio;

// Connect to Cumul.io API
$client = Cumulio::initialize('663e487c-1879-469d-8b1e-1fb291556a7c', 'ZSnLjKso3yOQ68yVrAEHJEUeP6ekIG8pSrYuju7Z8ZXW9IA8x8vQjlNRQ2WbCErPR0izAribxk4UfI9unRjq9YPV7dh6i9xWTblfAD4Wuo4TPJPisbVA7smm35osWBJ3ctFpCwIng3FdyyFgVh2aux'); // Fill in your API key & token
// $client = Cumulio::initialize('88b7627b-24f9-4a67-a458-77265d222efe', 'eC6d5fD4k9HVMrTBKhIr8thlTKpffJ8IQ6l2rggSqUPvOz3iHxoK89q0mkxa7pjZL05DISU0P74VL0hQbajN9ciwzoh6HqjptzJ21lNEnu4PMBnsZ9ahCf4aTcKxoS1DUfwyOPDbb82NiFwtnLgTSK'); // Fill in your API key & token

// Set third, optional property to https://api.cumul.io/ (default, EU multitenant env), https://api.us.cumul.io (US multitenant env) or your specific VPC address

// On page requests of pages containing embedded dashboards, request an "authorization"
$integrationId = 'fdf0188c-28c6-44f5-ba27-6d95a958fb66'; // WMS Integration
// $integrationId = '7cc1d234-8fc3-44ac-be65-02f510941f34'; // Supplier Integration
$authorization = $client->create('authorization', array(
  'type' => 'sso',
  'integration_id' => $integrationId,
  'expiry' => '24 hours',
  'inactivity_interval' => '10 minutes',
  // user information
  'username' => '12345678', // unique, immutable username
  'name' => 'John Doe',
  'email' => 'johndoe@burritosnyc.com',
  'suborganization' => 'Burritos NYC',
  'role' => 'viewer',
  // data restrictions 
  'metadata' => array(
    'company_id' => [31]
    ,'office_id' => [40]
    ,'department_id' => [14,15,16,18]
  )
));
?>


<!DOCTYPE html>
<html>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Cumul.io embedding example</title>
  </head>
  <style>
  img { 
    width: 1000px; height: auto;
    ALIGN-ITEMS: CENTER !important; 
  }

  .nav-link {
    color:#bfbfbf;
  }
  .nav-link:hover,.nav-link:active {
    color:#333333b3 !important;
  }

  .btn-secondary, .btn-secondary:visited {
    background-color: #343544 !important;
    border-color: #343544 !important;
  }
  .btn-primary, .btn-primary:visited {
    background-color: #f3b329 !important;
    border-color: #f3b329 !important;
  }
  .btn-primary:hover, .btn-primary:active {
    background-color: #936b14 !important;
    border-color: #936b14 !important;
  }

  .modal-dialog {
    max-width: 1030px !important;
  }
  </style>
  <body style="font-family: sans-serif;">

    <div class="panel-body">
      <br>
      <ul id="tabs" class="nav nav-tabs"></ul> 
      <cumulio-dashboard
          appServer="https://app.cumul.io/">  
          <!-- Set appServer to https://app.cumul.io/ (default, EU multitenant env), https://app.us.cumul.io (US multitenant env) or your specific VPC address -->
      </cumulio-dashboard>
    </div>

    <!-- Heat Modal -->
    <div class="modal fade" id="heatmap" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body" id="heatm">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save heatmap</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Check out the latest version on our npm page, as well as our components for frameworks such as react, vue and angular -->
    <script src="https://cdn-a.cumul.io/js/cumulio-dashboard/2.0.1/cumulio-dashboard.min.js" charset="utf-8"></script>
    <script type="text/javascript">
      
      const dashboardElement = document.querySelector('cumulio-dashboard');
      // We can now set the key and token to the dashboard component.
      dashboardElement.authKey = '<?php echo $authorization['id']; ?>'
      dashboardElement.authToken ='<?php echo $authorization['token']; ?>'
      // retrieve the accessible dashboards from the Integration
      dashboardElement.getAccessibleDashboards()
        .then(dashboards => {
          const tabs = document.getElementById('tabs');
          if (dashboards.length > 0) {
            
            dashboards.sort(function (a, b) {
              return a.slug.localeCompare(b.slug)});

            //remove this ligne after test
            dashboardElement.dashboardId = dashboards[0].id; // select Occupancy dashboard
            
            dashboards.forEach((dashboard, i) => {
              const newTab = document.createElement('li');
              
              // newTab.classList.add('nav-item');
              // newTab.innerHTML = `<a class="nav-link">${dashboard.name ? dashboard.name : 'Dashboard -' + index}</a>`;
              // if (index === 0) newTab.classList.toggle('active');

              if (i === 0) {
              newTab.classList.toggle('active');
              newTab.classList.add('nav-item');
              newTab.innerHTML = `
                  <a class="nav-link active">${dashboard.name ? dashboard.name : 'Dashboard -' + i}</a>
                `} else {
              newTab.classList.add('nav-item');
              newTab.innerHTML = `
                  <a class="nav-link">${dashboard.name ? dashboard.name : 'Dashboard -' + i}</a>
                `};
              
              newTab.onclick = () => {
                dashboardElement.dashboardId = dashboard.id;
                const options = tabs.querySelectorAll('li');
                for (const option of options) {
                  if (option.isSameNode(newTab)) option.childNodes[1].classList.add('active');
                  else option.childNodes[1].classList.remove('active');
                }
              };
              tabs.appendChild(newTab);
            });
          }
        });

        dashboardElement.addEventListener('customEvent', (event) => { 
        const modal = document.getElementById('heatm');
        
        if (event.detail.data.data.event == 'heatmap'){
          modal.innerHTML=`<img src="fig.png" alt="heatmap"/>`
        } else {
          modal.innerHTML=`<img src="movie.gif" alt="heatmap"/>`
        }
        $(document).ready(function(){
            $("#heatmap").modal("show");
          });
        });
    </script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>