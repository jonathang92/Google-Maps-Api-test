<?php
  if (isset($_GET['direccion'])) {
    $direccion= $_GET['direccion'];
    $cantidad= $_GET['cantidad'];
    // Aqui se coloca la llave obtenida al crear una aplicacion en la consola de Google para desarrolladores
    $KEY="";
    // echo "Dirección: ".$direccion;

    // $gmaps_URL = "https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=".$KEY;
    $gmaps_URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($direccion)."&key=".$KEY;

    // obtener respuesta
    $gmaps_json = file_get_contents($gmaps_URL);

    //Convertir en Arreglo
    $gmaps_array = json_decode($gmaps_json,true);
    // echo $gmaps_URL;
    // echo "<br>";
    // echo "<pre>";
    // print_r($gmaps_array);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($gmaps_array['results'][0]['geometry']['location']);
    // echo "</pre>";

    $latitud = $gmaps_array['results'][0]['geometry']['location']['lat'];
    $longitud = $gmaps_array['results'][0]['geometry']['location']['lng'];


    //****************wikimedia API***************************

    // $wikimedia_URL = "https://commons.wikimedia.org/w/api.php?format=json&action=query&generator=geosearch&ggsprimary=all&ggsnamespace=6&ggsradius=500&ggscoord=51.5|11.95&ggslimit=2&prop=imageinfo&iilimit=1&iiprop=url&iiurlwidth=200&iiurlheight=200";
    $wikimedia_URL = "https://commons.wikimedia.org/w/api.php?format=json&action=query&generator=geosearch&ggsprimary=all&ggsnamespace=6&ggsradius=2000&ggscoord=".$latitud."|".$longitud."&ggslimit=".$cantidad."&prop=imageinfo&iilimit=1&iiprop=url&iiurlwidth=200&iiurlheight=200";
    // echo $wikimedia_URL;
    // obtener respuesta
    $wikimedia_json = file_get_contents($wikimedia_URL);

    //convertir en arreglo
    $wikimedia_array = json_decode($wikimedia_json,true);
    if (isset($wikimedia_array['query'])) {
      // echo "<pre>";
      //   print_r($wikimedia_array);
      // echo "</pre>";
      $pages = $wikimedia_array['query']['pages'];
    } else{
      $adv="No Hay Resultados :(";
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Google Maps API Test</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style media="screen">
       .pagina{
         margin-top: 100px;
       }
       h1{
         text-align: center;
         margin-bottom: 30px;
       }
    </style>
  </head>
  <body>
    <div class="container-fluid pagina">
      <h1>Prueba de API de Google Maps y Wikimedia</h1>
      <div class="col-md-4 well">
        <form class="form-horizontal" action="" method="GET">
          <fieldset>
            <div class="form-group">
              <label for="direccion" class="col-lg-4 control-label">Ingrese dirección</label>
              <div class="col-lg-8">
                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Direccion" value="<?php if (isset($_GET['direccion'])) { echo $_GET['direccion']; } ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label for="cantidad" class="col-lg-6 control-label">Ingrese cantidad de imagenes</label>
              <div class="col-lg-6">
                <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="10" value="<?php if (isset($_GET['cantidad'])) { echo $_GET['cantidad']; } else {echo '1';} ?>" min="1" max="50" required>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary">Consultar</button>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="col-md-8">
        <?php
        if(isset($adv)){
          echo $adv;
        }
        if ((isset($pages)) AND (count($pages)>0)) {
          foreach ($pages as $key => $page) {
            // echo "<pre>";
            //   print_r($page['imageinfo']);
            // echo "</pre>";
            ?>
            <a href="<?php echo $page['imageinfo'][0]['descriptionurl']; ?>" target="_blank">
              <img src="<?php echo $page['imageinfo'][0]['thumburl']; ?>" alt="<?php echo $page['title']; ?>" height="<?php echo $page['imageinfo'][0]['thumbheight']; ?>" width="<?php echo $page['imageinfo'][0]['thumbwidth']; ?>" class="img-responsive img-thumbnail">
            </a>
            <?php
          }
        }

        ?>
      </div>
    </div>
  </body>
</html>
