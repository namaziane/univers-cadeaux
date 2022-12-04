jQuery(document).ready(function( $ ) {
  $('.eseot-toggle').click(function(e) {
  	e.preventDefault();
    
    $('a.eseot-toggle.open').not(this).removeClass('open');
    
    var $this = $(this);
    if($this.hasClass('open')) {
      $this.removeClass('open');
    } else {
      $this.addClass('open');
    }
    
    if ($this.next().hasClass('show')) {
        $this.next().removeClass('show');
        $this.next().slideUp(350);
    } else {
        $this.parent().parent().find('li .inner').removeClass('show');
        $this.parent().parent().find('li .inner').slideUp(350);
        $this.next().toggleClass('show');
        $this.next().slideToggle(350);
    }
  });
});