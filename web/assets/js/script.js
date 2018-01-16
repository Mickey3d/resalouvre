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


  // configure the bootstrap datepicker
  $('.booking-datepicker').datepicker({
    startView: 2,
    format: 'dd-mm-yyyy',
    clearBtn: true,
    language: "fr",
    startDate: 'today',
   // endDate: '',
    startView: 1,
    defaultViewDate: { year: 1980, month: 06, day: 15 },
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

  stepTwo.style.display = 'none';

  $('#checkAvailability').click(function(e) {
    
    var dateToCheck = dateBookingForElement.value;
    var url = 'booking/check-availability/' + dateToCheck;
    var typeToCheck0 = bookingTypeHalfDayElt.checked;
    var typeToCheck1 = bookingTypeDayElt.checked;

    if (dateToCheck != "" & (typeToCheck0 === true | typeToCheck1 === true))
    {

      console.log(url);
      console.log('Date OK')
      $.get(url) 
        .done(function( data ) {

          if(errorInfoDateElt.textContent != "" | errorInfoTypeElt.textContent != "")
          {
            errorInfoDateElt.textContent = "";
            errorInfoTypeElt.textContent = "";
          }

          if(data.availability > 0)
          {
            console.log('Disponibilité OK : ' + data.availability)
            stepTwo.style.display = '';
          }
          else
          {
            console.log('Date indisponible : ' + data.errors)
          }

        alert( "Data Loaded: " + ' CONFIG: ' + data.config_name + ' DISPO: ' + data.availability + ' LIMITE TICKETS ' + data.tickets_limit + ' ERREUR: ' + data.errors + ' MESSAGE: ' + data.messages );
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
      format: 'dd-mm-yyyy',
      clearBtn: true,
      language: "fr",
      startDate: '01/01/1900',
      endDate: '-1d',
      startView: 3,
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


