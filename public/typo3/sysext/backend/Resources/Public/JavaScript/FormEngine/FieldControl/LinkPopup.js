/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
define(["require","exports","TYPO3/CMS/Core/DocumentService","TYPO3/CMS/Backend/FormEngine","../../Modal"],(function(e,t,n,o,r){"use strict";return class{constructor(e){this.controlElement=null,this.handleControlClick=e=>{e.preventDefault();const t=this.controlElement.dataset.itemName,n=this.controlElement.getAttribute("href")+"&P[currentValue]="+encodeURIComponent(document.forms.namedItem("editform")[t].value)+"&P[currentSelectedValues]="+encodeURIComponent(o.getFieldElement(t).val());r.advanced({type:r.types.iframe,content:n,size:r.sizes.large})},n.ready().then(()=>{this.controlElement=document.querySelector(e),this.controlElement.addEventListener("click",this.handleControlClick)})}}}));