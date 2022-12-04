jQuery(document).ready(function( $ ) {
  $('a.action').click(function(e) {
    e.preventDefault();
    
    var $this = $(this);
    
    var action = $this.attr('data-action');
    
    
    switch(action) {
      case 'remove':
        var row = $this.parent('span').parent('td').parent('tr');
        var cat = row.clone();
        cat.find('span.actions').remove();
        cat = cat.find('td.cat-name').text();
        
        row.remove();
        
        notice( '<strong>'+cat+' will be removed upon saving, save changes to confirm.', 'category');
        
      break;
      case 'remove-source':
        var row = $this.parent('span').parent('td').parent('tr');
        var source = row.clone();
        source.find('span.actions').remove();
        source = source.find('td.source-name').text();
        
        row.remove();
        
        notice( '<strong>'+source+'</strong> will be removed upon saving, save changes to confirm.', 'source');
      break;
    }
    
    function notice(noticeText = '', type = false) {
      
      if(false == type) {
        return;
      }
      
      var noticeTo = '';
      
      switch(type) {
        case 'category':
          noticeTo = $('.eseot-categories-wrapper'); 
        break;
        case 'source':
          noticeTo = $('.eseot-sources-wrapper');
        break;
      }

      noticeTo.prepend( '<div class="notice notice-info"><p>'+noticeText+'</p></div>' );
      
    }
    
  });
});