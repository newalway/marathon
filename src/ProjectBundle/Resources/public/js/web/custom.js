function slugify(text) {
  return text.toString().toLowerCase()
	.replace(/\s+/g, '-')           // Replace spaces with -
	// .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
	.replace(/\-\-+/g, '-')         // Replace multiple - with single -
	.replace(/^-+/, '')             // Trim - from start of text
	.replace(/-+$/, '');            // Trim - from end of text
}

function get_tracking_url(tracking_url, tracking_number){
  var value = '';
  if(tracking_url && tracking_number){
    value = tracking_url.replace("#ID#", tracking_number);
  }
  return value;
}
