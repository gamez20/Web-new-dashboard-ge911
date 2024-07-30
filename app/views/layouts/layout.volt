<style>
    .bg-black {
        background: rgba(10,10,10,1);
        background: -moz-linear-gradient(-45deg, rgba(10,10,10,1) 0%, rgb(33, 33, 33) 100%);
        background: -webkit-linear-gradient(-45deg, rgba(10,10,10,1) 0%, rgb(29, 29, 29) 100%);
        background: -o-linear-gradient(-45deg, rgba(10,10,10,1) 0%, rgb(29, 29, 29) 100%);
        background: -ms-linear-gradient(-45deg, rgba(10,10,10,1) 0%, rgb(25, 25, 25) 100%);
        background: linear-gradient(135deg, rgba(10,10,10,1) 0%, rgb(30, 30, 30) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0a0a0a', endColorstr='#292929', GradientType=1 );
    }
    .gmnoprint {
        max-height: 50px;
        float: left;
        max-width: 120px;
    }
    /*
    https://cdn.techinasia.com/wp-content/uploads/2015/06/18154117490_f2705fb0d0_k.jpg
    http://decostar.mx/images/giphy-downsized.gif?crc=463412413
    https://hdwallsource.com/img/2014/10/aurora-borealis-wallpaper-5068-5188-hd-wallpapers.jpg
    https://78.media.tumblr.com/89c01e245e854d3b84205008cccba06d/tumblr_o46qjbeZP51rvn6njo1_500.gif
    https://wallpapertag.com/wallpaper/full/2/3/3/486469-free-world-map-desktop-background-2560x1600-for-android-40.jpg
    */
    .video_div {
        width: 18%;
        position: fixed;
        bottom: 50px;
        z-index: 60;
        transition: 300ms !important;
    }
    .video_div_see {
        position: absolute;
        margin-left: 30%;
        margin-top: 200px;
        transition: 300ms !important;
    }
    .video_div_see iframe {
        width:800px;
        height:400px;
    }
    #div-map {width:600px;height:630px;border-radius: 100%;overflow: hidden !important; }
    #div-map div {height:100%;}

</style>
<ul class="sidebar-fixed-scroll sidebar-menu text-white col-md-2 col-sm-6 col-xs-12 shadow-1 hidden-xs hidden-sm">
    <li><a href="<?= PAYCORES_HOST_NAME ?>">GE-911</a></li><a href="#" class="hidden">C</a>
    <li class="nav-dropdown"><a href="#"><span class="fa fa-fighter-jet"></span>Fuerzas</a>
        <ul>
            <li><a href="<?= PAYCORES_HOST_NAME ?>principal/activeForce"> Fuerzas</a> </li>
            <li><a href="<?= PAYCORES_HOST_NAME ?>principal/forces"> Fuerzas registradas</a> </li>
        </ul>
    </li>
    <li><a href="<?= PAYCORES_HOST_NAME ?>user-adm"><span class="fa fa-users"></span>Usuarios</a> </li>
    <li class="nav-dropdown"><a href="#"><span class="fa fa-ambulance"></span>Emergencias</a>
        <ul>
            <li><a href="<?= PAYCORES_HOST_NAME ?>emergency/allEmergency">Emergencias</a></li>
            <li><a href="<?= PAYCORES_HOST_NAME ?>emergency/registerEmergency">Registrar emergencias</a></li>
            <li><a href="<?= PAYCORES_HOST_NAME ?>emergency/graphicData">Graphic data</a></li>
        </ul>
    </li>
    <li><a href="<?= PAYCORES_HOST_NAME ?>button"><span class="fa fa-hand-o-up"></span>Botones</a> </li>
     <li><a href="<?= PAYCORES_HOST_NAME ?>reportespdf"><span class="fa fa-file-pdf-o"></span>Reportes</a> </li>
    <li class="push-50-top"><h5>Otras opciones</h5></li>

    <li><a href="<?= PAYCORES_HOST_NAME ?>config"><span class="fa fa-cogs"></span>Configuracion</a></li>
    <?php if(isset($ROOTExist)){ ?>
        <li class="nav-dropdown"><a href="#"><span class="fa fa-warning"></span>DATA ROOT</a>
            <ul>
                <li><a href="<?= PAYCORES_HOST_NAME ?>location">User Data</a></li>
                <li><a href="<?= PAYCORES_HOST_NAME ?>location/adminData">Admin Data</a></li>
            </ul>
        </li>
    <?php } ?>
    <li><a href="<?= PAYCORES_HOST_NAME ?>session/closeSession"><span class="fa fa-power-off"></span>LogOut</a></li>
</ul>
<div class="col-md-10 remove-padding col-md-offset-2">
    {{ content() }}
</div>

<script src="<?= PAYCORES_HOST_NAME ?>node_modules/push.js/bin/push.js"></script>
<script>
    $.ajax( {
        url:"<?= PAYCORES_HOST_NAME ?>principal/reloadMap", type:"post"
        , success:function(t) {
            if(obj=JSON.parse(t), obj.status) {
                var r=obj.data;
                if(r.length>markersEmerg.length){
                    Push.create("Nueva notificación", {
                        body: 'Emergencia!',
                        icon: '"<?= PAYCORES_HOST_NAME ?>img/logox300.png',
                        timeout: 6000,
                        onClick: function () {
                            window.focus();
                            this.close();
                        }
                    });
                    playSound();
                }else if(r.length<=markersEmerg.length){
                }
            }
        }
    })
    /*var camera, scene, renderer;
    var geometry, material, mesh;

    init();
    animate();

    function init() {

        camera = new THREE.PerspectiveCamera( 70, window.innerWidth / window.innerHeight, 0.01, 10 );
        camera.position.z = 1;

        scene = new THREE.Scene();

        geometry = new THREE.BoxGeometry( 0.2, 0.2, 0.2 );
        material = new THREE.MeshNormalMaterial();

        mesh = new THREE.Mesh( geometry, material );
        scene.add( mesh );

        renderer = new THREE.WebGLRenderer( { antialias: true } );
        renderer.setSize( window.innerWidth, window.innerHeight );
        document.body.appendChild( renderer.domElement );

    }

    function animate() {

        requestAnimationFrame( animate );

        mesh.rotation.x += 0.01;
        mesh.rotation.y += 0.02;

        renderer.render( scene, camera );

    }*/
</script>