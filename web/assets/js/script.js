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

    // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
    index++;
  }

  // La fonction qui ajoute un lien de suppression d'un ticket
  function addDeleteLink($prototype) {
    // Création du lien
    var $deleteLink = $('<a href="#" class="btn btn-delete"><i class="fa fa-trash-o" aria-hidden="true"></i><strong> Supprimer</strong></a></br><hr height="none" size="3" width="50%" color="blue">');

    // Ajout du lien
    $prototype.append($deleteLink);

    // Ajout du listener sur le clic du lien pour effectivement supprimer le ticket
    $deleteLink.click(function(e) {
      $prototype.remove();

      e.preventDefault(); // évite qu'un # apparaisse dans l'URL
      return false;
    });
  }

  // configure the bootstrap datepicker
  $('.js-datepicker').datepicker({
    startView: 2,
    format: 'yyyy/MM/dd',
    autoclose: true,
    todayHighlight: true,
    clearBtn: true,
    language: "fr"
  })


});
