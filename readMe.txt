info: https://www.t3sbootstrap.de/


Scrollify & scss aus contrib entfernt




- highlightjs 9.17.1
-jquery.mb.YTPlayer/ 3.2.11



noLazyloadTemplate ?? 
&& image no title ??? 
NoLazyloadImage ??? -> rendering/xl-NoLazyloadImage.html in open file

- form komplett entfernt!

NEW - SectionIndex (menu) - revised -> option für gridelement
- NEW: 	# cat=bootstrap-config/d-config/050; type=options[none=0, mt-1=mt-1, mt-2=mt-2, mt-3=mt-3, mt-4=mt-4, mt-5=mt-5]; label=Content Element Default Top-Margin: here you can set a default space (margin-top) for each content-element (colPos=0).
	contentMarginTop = 0

- constants - lightbox = none

- menu_recently_updated


- poster for local videos if JPG-image in fileadmin/user_upload/ with same name as video <f:variable name="poster">{fileParts.path}{fileParts.filebody}.jpg</f:variable>


 # FA Pro - extra color for duoton
.fad::before {
	--fa-primary-color: var(--primary);
	--fa-primary-opacity: 1;
}
.fad::after {
	--fa-secondary-color: var(--secondary);
	--fa-secondary-opacity: 0.3;
}


- FIX: animate css ohne 
- FIX: FA icons webfonts von Pro nicht von Free



 ####################################
 
contentElement/Default
lib/ContentElement
ConfigProzessor
ext_localconf

Templates:
layout/content/default
Card
TextmediaTextWithImage
Gallery
Type
Image
Image
RawGallery
Carousel
All
MenuSection
BackgroundWrapper
Main
Mediaobject

BE/layoutDefault


 
 ####################################
 
 ToDo:
 
page {
	meta {
		keywords.data = levelfield :-1, keywords, slide
		keywords.override.field = keywords
	}
}

@news & abstract testen!
 
 