
var listamarkers=[];

function marker(id) {

    var presiono=id.getAttribute('id');
    document.getElementById("labelinfo").value=presiono.replace("_"," ");

    if(presiono=="RESTAURAR")
    {
        restaurarMarker();
    }

    else {

        var opcion=presiono;
        opcion=opcion.replace("_"," ");

        if(comprobarEmergencia(opcion,"EMERGENCIA"))
        {
            for(var i=0;i<listanmarkers.length;i++)
            {
                var separar=listamarkers[i].tipo.split(",");
                if(separar[1]!==opcion)
                {
                    listamarkers[i].setVisible(false);
                }
            }
        }

    }
}

function comprobarEmergencia(opcion,tipo)
{
    for(var i=0;i<listamarkers.length;i++)
    {
        var separar=listamarkers[i].tipo.split(",");
        if(tipo==='EMERGENCIA')
        {
            if(separar[1]===opcion)
            {
                return true;
            }
        }
        else if(separar[0]===opcion)
        {
            return true;
        }
    }
    return false;
}
function restaurarMarker()
{
    for (var i=0;i<listamarkers.length;i++)
    {
        if(listamarkers[i].visible==false)
            listamarkers[i].setVisible(true);
    }
}

function cambio(id) {

    var presiono=id.getAttribute('id');
    document.getElementById("labelinfo").value=presiono.replace("_"," ");

    var valor=document.getElementById("data").value;
    if(Funcion())
    {
        if(comprobarEmergencia(valor,"FECHA"))
        {
            for(var i=0;i<listamarkers.length;i++)
            {
                var separar=listamarkers[i].tipo.split(",");

                if(separar[0]!==valor)
                {
                    listamarkers[i].setVisible(false);
                }
            }
        }
        else
            alert("no se encontraron emergencias con la fecha.phtml:"+valor);
    }

}

function Funcion()
{
    var valor = document.getElementById("data").value;

    if(valor.length!=0)
    {
        if(validarFecha(valor))
        {
            return true;
        }
        else
        {
            alert("Por favor revise la fecha que sea correcta dd-mm-yy");
            return false;
        }
    }
    else
    {
        alert("debe seleccionar una fecha ");
        return false;
    }
}

function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];

    return $_SERVER['REMOTE_ADDR'];
}
/* Otherwise just put the config content (json): */

particlesJS('particles-js',

    {
        "particles": {
            "number": {
                "value": 80,
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#666364"
            },
            "shape": {
                "type": " ",
                "stroke": {
                    "width": 0,
                    "color": "#000000"
                },
                "polygon": {
                    "nb_sides": 5
                },
                "image": {
                    "src": "img/github.svg",
                    "width": 100,
                    "height": 100
                }
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {
                    "enable": false,
                    "speed": 1,
                    "opacity_min": 0.1,
                    "sync": false
                }
            },
            "size": {
                "value": 5,
                "random": true,
                "anim": {
                    "enable": false,
                    "speed": 40,
                    "size_min": 0.1,
                    "sync": false
                }
            },
            "line_linked": {
                "enable": true,
                "distance": 150,
                "color": "#ffffff",
                "opacity": 0.4,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 6,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 1200
                }
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "repulse"
                },
                "onclick": {
                    "enable": true,
                    "mode": "push"
                },
                "resize": true
            },
            "modes": {
                "grab": {
                    "distance": 400,
                    "line_linked": {
                        "opacity": 1
                    }
                },
                "bubble": {
                    "distance": 400,
                    "size": 40,
                    "duration": 2,
                    "opacity": 8,
                    "speed": 3
                },
                "repulse": {
                    "distance": 200
                },
                "push": {
                    "particles_nb": 4
                },
                "remove": {
                    "particles_nb": 2
                }
            }
        },
        "retina_detect": true,
        "config_demo": {
            "hide_card": false,
            "background_color": "#b61924",
            "background_image": "",
            "background_position": "50% 50%",
            "background_repeat": "no-repeat",
            "background_size": "cover"
        }
    }

);