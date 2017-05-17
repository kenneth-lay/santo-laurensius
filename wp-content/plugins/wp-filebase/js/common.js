var wpfbFileInfos = [];

function wpfilebase_filedetails(id) {
	var dtls = document.getElementById('wpfilebase-filedetails' + id);
	if(dtls) dtls.style.display = (dtls.style.display!='none') ? 'none' : 'block';	
	return false;
}

function wpfb_getFileInfo(url)
{
	var i,fi,uesc=unescape(url);
	for(i = 0; i < wpfbFileInfos.length; i++) {
		fi = wpfbFileInfos[i];
		if(fi.url == url || fi.url == uesc)	return fi;
	}
	try{// to get url by ajax request
		// wpfbfid
		fi = jQuery.parseJSON(jQuery.ajax({url:wpfbConf.ajurl,data:{action:"fileinfo",url:uesc},async:false}).responseText);
		if(typeof(fi) == 'object' && fi.id > 0) {
			wpfbFileInfos.push(fi);
			return fi;		
		}
	}
	catch(err){}	
	return null;
}

function wpfb_ondownload(url) {
	if(typeof(url) == 'object') url = url.data;
	if(typeof(wpfb_ondl) == 'function') {
		var fi = wpfb_getFileInfo(url);
		if(fi != null) {
			try { wpfb_ondl(fi.id,'/'+wpfbConf.db+'/'+fi.path,fi.path); }
			catch(err){}
		}
	}
}

function wpfb_onclick(event) {
	wpfb_ondownload(event);	
	if(wpfbConf.hl) { window.location=event.data; return false; } // hide links
	return true;
}

function wpfb_processlink(index, el) {
	var url=el.getAttribute('href'),i;
	el = jQuery(el);
	if((i=url.indexOf('#')) > 0) {
		var fid = url.substr(i);
		fid = fid.substr(fid.lastIndexOf('-')+1);
		el.attr('wpfbfid', fid);
		url = url.substr(0, i); // remove hash, not actually needed
	}
	el.unbind('click').click(url, wpfb_onclick); // bind onclick
	if(wpfbConf.cm && typeof(wpfb_addContextMenu) == 'function') wpfb_addContextMenu(el, url);
	if(wpfbConf.hl) url = 'javascript:;';
	el.attr('href', url);
}

function wpfb_processimg(index, el)
{
	jQuery(el).unbind('load').load(el.src, wpfb_ondownload);
}

function wpfb_setupLinks() {
	var i,els,h,rePl,reQs,reHs;
	if(!wpfbConf.ql) return;

	reQs = /\?wpfb_dl=([0-9]+)$/;
	reHs = /#wpfb-file-([0-9]+)$/;
	rePl = new RegExp('^'+wpfbConf.hu+wpfbConf.db+'/');	
	
	els = document.getElementsByTagName('a');
	for(i=0;i<els.length;i++){
		h = els[i].getAttribute('href');
		if(h && (h.search(reQs)>0 || h.search(reHs)>0 || h.search(rePl)==0)) wpfb_processlink(i,els[i]);
	}
	
	els = document.getElementsByTagName('img');
	for(i=0;i<els.length;i++){
		h = els[i].getAttribute('src');
		if(h && (h.search(reQs)>0 || h.search(rePl)==0)) wpfb_processimg(i,els[i]);
	}
}

if(typeof(jQuery) != 'undefined')
	jQuery(document).ready(wpfb_setupLinks);