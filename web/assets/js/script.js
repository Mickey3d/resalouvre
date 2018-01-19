
$(document).ready(function() {
  

  $('#menu-topo').hide();
  var menuaberto = false;
  $('.btn-collapse').click(function(event) {
    event.preventDefault();
    $('#menu-topo').toggle('');
    if (menuaberto == true) {
      menuaberto = false;
      $(".lista-collapse:nth-child(1)").removeClass('botao-lista-cima');
      $(".lista-collapse:nth-child(2)").removeClass('hidden');
      $(".lista-collapse:nth-child(3)").removeClass('botao-lista-baixo');
    } else {
      menuaberto = true;
      $(".lista-collapse:nth-child(1)").addClass('botao-lista-cima');
      $(".lista-collapse:nth-child(2)").addClass('hidden');
      $(".lista-collapse:nth-child(3)").addClass('botao-lista-baixo');
    }
  });

  $('.alert').fadeIn();

  setTimeout(function() {
    $('.alert').fadeOut()
  }, 7000);



// STEP ONE CONFIG

  $('#stepOne').fadeIn(1500, 'swing');

  // configure the bootstrap datepicker
  $('.booking-datepicker').datepicker({
    startView: 1,
    format: 'dd-MM-yyyy',
    clearBtn: true,
    language: "fr",
    startDate: 'today',
    autoclose: true,
  })

// Var Access for Step One to Step Two
  var stepOne = document.getElementById("stepOne");
  var stepTwo = document.getElementById("stepTwo");
  var dateBookingForElement = document.getElementById("surikat_bookingbundle_booking_bookingFor");
  var errorInfoDateElt = document.getElementById("errorInfoDate");
  var bookingTypeHalfDayElt = document.getElementById("surikat_bookingbundle_booking_type_0");
  var bookingTypeDayElt = document.getElementById("surikat_bookingbundle_booking_type_1");
  var errorInfoTypeElt = document.getElementById("errorInfoType");
  var availabilityInfoElt = document.getElementById("availabilityInfo");
  var availabilityInfo = '';
// CHECK AVAILABILITY CLICK
  $('#checkAvailability').click(function(e) {
    
    var dateToCheck = dateBookingForElement.value;
    var url = 'booking/check-availability/' + dateToCheck;
    var typeToCheck0 = bookingTypeHalfDayElt.checked;
    var typeToCheck1 = bookingTypeDayElt.checked;

    if (dateToCheck != "" & (typeToCheck0 === true | typeToCheck1 === true))
    {
      var dateSplit = dateToCheck.split("-");

      switch (dateSplit[1]) {
        case 'janvier':
          dateSplit[1] = '01';
        break;
        case 'février':
          dateSplit[1] = '02';
        break;
        case 'mars':
          dateSplit[1] = '03';
        break;
        case 'avril':
          dateSplit[1] = '04';
        break;
        case 'mai':
          dateSplit[1] = '05';
        break;
        case 'juin':
          dateSplit[1] = '06';
        break;
        case 'juillet':
          dateSplit[1] = '07';
        break;
        case 'août':
        dateSplit[1] = '08';
        break;
        case 'septembre':
          dateSplit[1] = '09';
        break;
        case 'octobre':
          dateSplit[1] = '10';
        break;
        case 'novembre':
          dateSplit[1] = '11';
        break;
        case 'décembre':
          dateSplit[1] = '12';
        break;
        default:
          dateSplit[1] = 'null';
      };

      dateToCheck = dateSplit[0] + '-' + dateSplit[1] + '-' + dateSplit[2];
      url = 'booking/check-availability/' + dateToCheck;
      console.log(url);

      $.get(url) 
        .done(function( data ) {

          if(errorInfoDateElt.textContent != "" | errorInfoTypeElt.textContent != "")
          {
            errorInfoDateElt.textContent = "";
            errorInfoTypeElt.textContent = "";
          }

          if(data.availability > 0)
          {
            console.log('Disponibilité OK : ' + data.availability);
            availabilityInfo = 'Des places sont encore disponibles ! Vous pouvez commandez jusqu\'à ' + data.tickets_limit + ' Tickets.';
            addAvailabilityInfo(availabilityInfoElt, availabilityInfo);
            $('#stepOne').fadeOut(500, 'linear');
            $('#configInfo').fadeIn(1000, 'linear');
            $('#stepTwo').fadeIn(2000, 'swing');
        
          }
          else
          {
            console.log('Date indisponible : ' + data.errors);
            availabilityInfo = 'Malheureusement cette date n\'est pas disponible. ' + data.errors;
            addAvailabilityInfo(availabilityInfoElt, availabilityInfo);
            $('#configInfo').slideDown(800, 'linear');
            $('#stepTwo').fadeOut(1000, 'linear');
          }

     //   alert( "Data Loaded: " + ' CONFIG: ' + data.config_name + ' DISPO: ' + data.availability + ' LIMITE TICKETS ' + data.tickets_limit + ' ERREUR: ' + data.errors + ' MESSAGE: ' + data.messages );
      });

    }
    else
    {
      if(dateToCheck == "")
      {
        //console.log('date error 1');
        if(errorInfoDateElt.textContent === "")
        {
          addErrorInfoDate(errorInfoDateElt); 
        }
      }
      if(dateToCheck == "" & typeToCheck0 === true | typeToCheck1 === true)
      {
        //console.log('date error 2');
        if(errorInfoTypeElt.textContent != "")
        {
          errorInfoTypeElt.textContent = ""; 
        }
      }

      if(typeToCheck0 === false & typeToCheck1 === false)
      {
        //console.log('type error 1');
        if(errorInfoTypeElt.textContent === "")
        {
        addErrorInfoType(errorInfoTypeElt);
        }    
      }
      if(dateToCheck != "" & typeToCheck0 === false & typeToCheck1 === false)
      {
        //console.log('type error 2');
        if(errorInfoDateElt.textContent != "")
        {
          errorInfoDateElt.textContent = ""; 
        }
      }
    }
     
      // console.log($errorInfoDateElt.textContent);   

    e.preventDefault();
    return false;

  });

  function addErrorInfoDate(errorInfoDateElt) {
    var errorInfoDate = 'Veuillez selectionner une Date pour votre visite';
    errorInfoDateElt.append(errorInfoDate);
  }

  function addErrorInfoType(errorInfoTypeElt) {
    var errorInfoType = 'Veuillez selectionner la durée de votre visite';
    errorInfoTypeElt.append(errorInfoType);
  }

  function addAvailabilityInfo(availabilityInfoElt, availabilityInfo) {
    if(availabilityInfoElt.textContent != "")
        {
          availabilityInfoElt.textContent = ""; 
        }

    availabilityInfoElt.append(availabilityInfo);
  }



// STEP TWO CONFIG AND TICKETS ENGINE

  // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
  var $container = $('div#surikat_bookingbundle_booking_tickets');

  // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
  var index = $container.find(':input').length;

  // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
  $('#add_ticket').click(function(e) {
    addTicket($container);

    e.preventDefault(); // évite qu'un # apparaisse dans l'URL
    return false;
  });

  // On ajoute un premier champ automatiquement
  if (index == 0) {
    addTicket($container);
  } else {
    // On ajoute un lien de suppression pour chacun des tickets
    $container.children('div').each(function() {
      addDeleteLink($(this));
    });
  }

  // La fonction qui ajoute un formulaire TicketType
  function addTicket($container) {
    // Dans le contenu de l'attribut « data-prototype », on remplace :
    // - le texte "__name__label__" qu'il contient par le label du champ
    // - le texte "__name__" qu'il contient par le numéro du champ
    var template = $container.attr('data-prototype').replace(/__name__label__/g, 'Ticket n°' + (
      index + 1
    )).replace(/__name__/g, index);

    // On crée un objet jquery qui contient ce template
    var $prototype = $(template);

    // On ajoute au prototype un lien pour pouvoir supprimer le ticket
    addDeleteLink($prototype);

    // On ajoute le prototype modifié à la fin de la balise <div>
    $container.append($prototype);

    // configure the bootstrap datepicker
    $('.js-datepicker').datepicker({
      startView: 2,
      format: 'dd-MM-yyyy',
      clearBtn: true,
      language: "fr",
      startDate: '01/01/1900',
      endDate: '-1d',
      defaultViewDate: { year: 1980, month: 06, day: 15 },
      autoclose: true,
      })

    // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
    index++;
  }

  // La fonction qui ajoute un lien de suppression d'un ticket
  function addDeleteLink($prototype) {
    // Création du lien
    var $deleteLink = $('<div><a href="#" title="Supprimer de la Commande" class="pull-right btn btn-delete"><i class="fa fa-trash-o" aria-hidden="true"></i><strong> </strong></a></br><hr height="none" size="3" width="50%" color="blue"></div>');

    if (($('div#surikat_bookingbundle_booking_tickets > div').length) >= 1) {
    // Ajout du lien
    $prototype.append($deleteLink);
    }
    // Ajout du listener sur le clic du lien pour effectivement supprimer le ticket
    $deleteLink.click(function(e) {
      if (($('div#surikat_bookingbundle_booking_tickets > div').length) > 1) {
                $prototype.remove();          
    }
      e.preventDefault(); // évite qu'un # apparaisse dans l'URL
      return false;
    });
  }


});