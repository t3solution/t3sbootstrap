<?php

namespace T3SBS\T3sbootstrap\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;


/*
 * This file is part of the TYPO3 extension t3sbootstrap.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class Config extends AbstractEntity
{


 	    
    /**
     * pid
     *
     * @var int

    protected ?int $pid = null;
     */
    /**
     * pid
     *
     * @var int

    protected $pid;
     */

    /**
     * company
     *
     * @var string
     */
    protected $company = '';

    /**
     * homepageUid
     *
     * @var int
     */
    protected $homepageUid = 0;

    /**
     * pageTitle
     *
     * @var string
     */
    protected $pageTitle = '';

    /**
     * pageTitlealign
     *
     * @var string
     */
    protected $pageTitlealign = '';

    /**
     * pageTitlecontainer
     *
     * @var string
     */
    protected $pageTitlecontainer = '';

    /**
     * pageTitleclass
     *
     * @var string
     */
    protected $pageTitleclass = '';

    /**
     * metaEnable
     *
     * @var string
     */
    protected $metaEnable = '';

    /**
     * metaValue
     *
     * @var string
     */
    protected $metaValue = '';

    /**
     * metaContainer
     *
     * @var string
     */
    protected $metaContainer = '';

    /**
     * metaClass
     *
     * @var string
     */
    protected $metaClass = '';

    /**
     * metaText
     *
     * @var string
     */
    protected $metaText = '';

    /**
     * navbarEnable
     *
     * @var string
     */
    protected $navbarEnable = '';

    /**
     * navbarEntrylevel
     *
     * @var int
     */
    protected $navbarEntrylevel = 0;

    /**
     * navbarLevels
     *
     * @var int
     */
    protected $navbarLevels = 0;

    /**
     * navbarExcludeuiduist
     *
     * @var string
     */
    protected $navbarExcludeuiduist = '';

    /**
     * navbarIncludespacer
     *
     * @var bool
     */
    protected $navbarIncludespacer = false;

    /**
     * navbarSectionmenu
     *
     * @var bool
     */
    protected $navbarSectionmenu = false;

    /**
     * navbarMegamenu
     *
     * @var bool
     */
    protected $navbarMegamenu = false;

    /**
     * navbarHover
     *
     * @var bool
     */
    protected $navbarHover = false;

    /**
     * navbarClickableParent
     *
     * @var bool
     */
    protected $navbarClickableparent = false;

    /**
     * navbarPlusicon
     *
     * @var bool
     */
    protected $navbarPlusicon = false;

    /**
     * navbarDropdownAnimate
     *
     * @var string
     */
    protected $navbarDropdownAnimate = '';

    /**
     * navbarDarkMode
     *
     * @var bool
     */
    protected $navbarDarkMode = false;


    /**
     * navbarBrand
     *
     * @var string
     */
    protected $navbarBrand = '';

    /**
     * navbarbrandAlignment
     *
     * @var string
     */
    protected $navbarbrandAlignment = '';

    /**
     * navbarImage
     *
     * @var string
     */
    protected $navbarImage = '';

    /**
     * navbarColor
     *
     * @var string
     */
    protected $navbarColor = '';

    /**
     * navbarBackground
     *
     * @var string
     */
    protected $navbarBackground = '';


    /**
     * navbarShrinkcolorschemes
     *
     * @var string
     */
    protected $navbarShrinkcolorschemes = '';

    /**
     * navbarShrinkcolor
     *
     * @var string
     */
    protected $navbarShrinkcolor = '';


    /**
     * navbarContainer
     *
     * @var string
     */
    protected $navbarContainer = '';

    /**
     * navbarInnercontainer
     *
     * @var string
     */
    protected $navbarInnercontainer = '';

    /**
     * navbarPlacement
     *
     * @var string
     */
    protected $navbarPlacement = '';

    /**
     * navbarAlignment
     *
     * @var string
     */
    protected $navbarAlignment = '';

    /**
     * navbarClass
     *
     * @var string
     */
    protected $navbarClass = '';

    /**
     * navbarToggler
     *
     * @var string
     */
    protected $navbarToggler = '';

    /**
     * navbarAnimatedtoggler
     *
     * @var bool
     */
    protected $navbarAnimatedtoggler = false;

    /**
     * navbarBreakpoint
     *
     * @var string
     */
    protected $navbarBreakpoint = '';

    /**
     * navbarOffcanvas
     *
     * @var bool
     */
    protected $navbarOffcanvas = false;

    /**
     * navbarTransparent
     *
     * @var bool
     */
    protected $navbarTransparent = false;

    /**
     * navbarHeight
     *
     * @var int
     */
    protected $navbarHeight = 0;

    /**
     * navbarSearchbox
     *
     * @var string
     */
    protected $navbarSearchbox = '';

    /**
     * navbarLangmenu
     *
     * @var bool
     */
    protected $navbarLangmenu = false;

    /**
     * navbarRightMenuUidList
     *
     * @var string
     */
    protected $navbarRightMenuUidList = '';

    /**
     * navbarExtraRow
     *
     * @var bool
     */
    protected $navbarExtraRow = false;

    /**
     * navbarLangFlags
     *
     * @var bool
     */
    protected $navbarLangFlags = false;

    /**
     * jumbotronEnable
     *
     * @var bool
     */
    protected $jumbotronEnable = false;

    /**
     * jumbotronBgimage
     *
     * @var string
     */
    protected $jumbotronBgimage = '';

    /**
     * jumbotronAlignitem
     *
     * @var string
     */
    protected $jumbotronAlignitem = '';

    /**
     * jumbotronBgimageratio
     *
     * @var string
     */
    protected $jumbotronBgimageratio = '';

    /**
     * jumbotronRounded
     *
     * @var string
     */
    protected $jumbotronRounded = '';

    /**
     * jumbotronSlide
     *
     * @var bool
     */
    protected $jumbotronSlide = false;

    /**
     * jumbotronPosition
     *
     * @var string
     */
    protected $jumbotronPosition = '';

    /**
     * jumbotronContainer
     *
     * @var string
     */
    protected $jumbotronContainer = '';

    /**
     * jumbotronContainerposition
     *
     * @var string
     */
    protected $jumbotronContainerposition = '';

    /**
     * jumbotronClass
     *
     * @var string
     */
    protected $jumbotronClass = '';

    /**
     * jumbotronCarouselInterval
     *
     * @var int
     */
    protected $jumbotronCarouselInterval = false;

    /**
     * jumbotronCarouselPause
     *
     * @var bool
     */
    protected $jumbotronCarouselPause = false;

    /**
     * breadcrumbEnable
     *
     * @var bool
     */
    protected $breadcrumbEnable = false;

    /**
     * breadcrumbFaicon
     *
     * @var bool
     */
    protected $breadcrumbFaicon = false;

    /**
     * breadcrumbFaicononly
     *
     * @var bool
     */
    protected $breadcrumbFaicononly = false;

    /**
     * breadcrumbNotOnRootpage
     *
     * @var bool
     */
    protected $breadcrumbNotonrootpage = false;

    /**
     * breadcrumbCorner
     *
     * @var bool
     */
    protected $breadcrumbCorner = false;

    /**
     * breadcrumbBottom
     *
     * @var bool
     */
    protected $breadcrumbBottom = false;

    /**
     * breadcrumbPosition
     *
     * @var string
     */
    protected $breadcrumbPosition = '';

    /**
     * breadcrumbContainer
     *
     * @var string
     */
    protected $breadcrumbContainer = '';

    /**
     * breadcrumbContainerosition
     *
     * @var string
     */
    protected $breadcrumbContainerposition = '';

    /**
     * breadcrumbClass
     *
     * @var string
     */
    protected $breadcrumbClass = '';

    /**
     * sidebarEnable
     *
     * @var string
     */
    protected $sidebarEnable = '';

    /**
     * sidebarRightenable
     *
     * @var string
     */
    protected $sidebarRightenable = '';

    /**
     * sidebarEntrylevel
     *
     * @var int
     */
    protected $sidebarEntrylevel = 99;

    /**
     * sidebarLevels
     *
     * @var int
     */
    protected $sidebarLevels = 0;

    /**
     * sidebarExcludeuiduist
     *
     * @var string
     */
    protected $sidebarExcludeuiduist = '';

    /**
     * sidebarIncludespacer
     *
     * @var bool
     */
    protected $sidebarIncludespacer = false;

    /**
     * footerEnable
     *
     * @var bool
     */
    protected $footerEnable = false;

    /**
     * footerFluid
     *
     * @var bool
    protected $footerFluid = false;
     */

    /**
     * footerSlide
     *
     * @var bool
     */
    protected $footerSlide = false;

    /**
     * footerSticky
     *
     * @var bool
     */
    protected $footerSticky = false;

    /**
     * footerContainer
     *
     * @var string
     */
    protected $footerContainer = '';

    /**
     * footerContainerposition
     *
     * @var string
     */
    protected $footerContainerposition = '';

    /**
     * footerClass
     *
     * @var string
     */
    protected $footerClass = '';

    /**
     * footerPid
     *
     * @var int
     */
    protected $footerPid = 0;

    /**
     * expandedcontentEnabletop
     *
     * @var bool
     */
    protected $expandedcontentEnabletop = false;

    /**
     * expandedcontentSlidetop
     *
     * @var bool
     */
    protected $expandedcontentSlidetop = false;

    /**
     * expandedcontentContainerpositiontop
     *
     * @var string
     */
    protected $expandedcontentContainerpositiontop = '';

    /**
     * expandedcontentContainertop
     *
     * @var string
     */
    protected $expandedcontentContainertop = '';

    /**
     * expandedcontentClasstop
     *
     * @var string
     */
    protected $expandedcontentClasstop = '';

    /**
     * expandedcontentEnablebottom
     *
     * @var bool
     */
    protected $expandedcontentEnablebottom = false;

    /**
     * expandedcontentSlidebottom
     *
     * @var bool
     */
    protected $expandedcontentSlidebottom = false;

    /**
     * expandedcontentContainerpositionbottom
     *
     * @var string
     */
    protected $expandedcontentContainerpositionbottom = '';

    /**
     * expandedcontentContainerbottom
     *
     * @var string
     */
    protected $expandedcontentContainerbottom = '';

    /**
     * expandedcontentClassbottom
     *
     * @var string
     */
    protected $expandedcontentClassbottom = '';


    /**
     * generalRootline
     *
     * @var bool
     */
    protected $generalRootline = false;


    /**
     * generalRootline
     *
     * @var bool
     */
    protected $generalOverride = false;


    /**
     * contentOnlyOnRootpage
     *
     * @var bool
     */
    protected $contentOnlyOnRootpage = false;

    /**
     * compress
     *
     * @var bool
     */
    protected $compress = false;

    /**
     * disablePrefixComment
     *
     * @var bool
     */
    protected $disablePrefixComment = false;

    /**
     * containerError
     *
     * @var bool
     */
    protected $containerError = false;

    /**
     * slideLeftAside
     *
     * @var bool
     */
    protected $slideLeftAside = false;

    /**
     * slideRightAside
     *
     * @var bool
     */
    protected $slideRightAside = false;

    /**
     * submenuSticky
     *
     * @var bool
     */
    protected $submenuSticky = false;


    /**
     * pageContentExtraClass
     *
     * @var string
     */
    protected $pageContentExtraClass = '';

    /**
     * bodyExtraClass
     *
     * @var string
     */
    protected $bodyExtraClass = '';

    /**
     * asideExtraClass
     *
     * @var string
     */
    protected $asideExtraClass = '';

    /**
     * mainExtraClass
     *
     * @var string
     */
    protected $mainExtraClass = '';

    /**
     * globalPaddingTop
     *
     * @var string
     */
    protected $globalPaddingTop = '';

    /**
     * stickyFooterExtraPadding
     *
     * @var int
     */
    protected $stickyFooterExtraPadding = 0;

    /**
     * contentMarginTop
     *
     * @var string
     */
    protected $contentMarginTop = '';

    /**
     * loadingSpinner
     *
     * @var string
     */
    protected $loadingSpinner = '';

    /**
     * loadingSpinnerColor
     *
     * @var string
     */
    protected $loadingSpinnerColor = '';

    /**
     * lightboxSelection
     *
     * @var string
     */
    protected $lightboxSelection = '';

    /**
     * magnifying
     *
     * @var bool
     */
    protected $magnifying = false;

    /**
     * sectionmenuAnchorOffset
     *
     * @var int
     */
    protected $sectionmenuAnchorOffset = 0;

    /**
     * sectionmenuScrollspyThreshold
     *
     * @var string
     */
    protected $sectionmenuScrollspyThreshold = '';
    /**
     * sectionmenuScrollspyRootMargin
     *
     * @var string
     */
    protected $sectionmenuScrollspyRootMargin = '';
    /**
     * sectionmenuStickyTop
     *
     * @var bool
     */
    protected $sectionmenuStickyTop = false;

    /**
     * sectionmenuScrollspy
     *
     * @var bool
     */
    protected $sectionmenuScrollspy = false;

    /**
     * backgroundImageEnable
     *
     * @var bool
     */
    protected $backgroundImageEnable = false;

    /**
     * backgroundImageSlide
     *
     * @var bool
     */
    protected $backgroundImageSlide = false;

    /**
     * shrinkingNavPadding
     *
     * @var string
     */
    protected $shrinkingNavPadding = '';

    /**
     * sidebarMenuPosition
     *
     * @var string
     */
    protected $sidebarMenuPosition = '';

    /**
     * $sectionmenuIcons
     *
     * @var bool
     */
    protected $sectionmenuIcons = false;

    /**
     * sidebarSectionMobile
     *
     * @var bool
     */
    protected $sidebarSectionMobile = false;

    /**
     * langMenuWithFaIcon
     *
     * @var string
     */
    protected $langMenuWithFaIcon = '';

    /**
     * subheaderColor
     *
     * @var string
     */
    protected $subheaderColor = '';

    /**
     * dateFormat
     *
     * @var string
     */
    protected $dateFormat = '';

    /**
     * favicon
     *
     * @var string
     */
    protected $favicon = '';

    /**
     * cardFlipperOnClick
     *
     * @var bool
     */
    protected $cardFlipperOnClick = false;

    /**
     * lastModifiedContentElement
     *
     * @var bool
     */
    protected $lastModifiedContentElement = false;

    /**
     * recentlyUpdatedContentElements
     *
     * @var bool
     */
    protected $recentlyUpdatedContentElements = false;



    /**
     * updated
     *
     * @var int
     */
    protected $updated = 0;

    /**
     * gridupdated
     *
     * @var int
     */
    protected $gridupdated = 0;




    /**
     * @param int<0, max> $pid
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @return int<0, max>|null
     */
#    public function getPid(): int|null
    public function getPid(): ?int
    {
        if ($this->pid === null) {
            return null;
        }
        return (int)$this->pid;
    }





    /**
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * Returns the homepageUid
     *
     * @return int $homepageUid
     */
    public function getHomepageUid()
    {
        return $this->homepageUid;
    }

    /**
     * Sets the homepageUid
     *
     * @param int $homepageUid
     * @return void
     */
    public function setHomepageUid($homepageUid): void
    {
        $this->homepageUid = $homepageUid;
    }

    /**
     * Returns the pageTitle
     *
     * @return string $pageTitle
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Sets the pageTitle
     *
     * @param string $pageTitle
     * @return void
     */
    public function setPageTitle($pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Returns the pageTitlealign
     *
     * @return string $pageTitlealign
     */
    public function getPageTitlealign()
    {
        return $this->pageTitlealign;
    }

    /**
     * Sets the pageTitlealign
     *
     * @param string $pageTitlealign
     * @return void
     */
    public function setPageTitlealign($pageTitlealign): void
    {
        $this->pageTitlealign = $pageTitlealign;
    }

    /**
     * Returns the pageTitlecontainer
     *
     * @return string $pageTitlecontainer
     */
    public function getPageTitlecontainer()
    {
        return $this->pageTitlecontainer;
    }

    /**
     * Sets the pageTitlecontainer
     *
     * @param string $pageTitlecontainer
     * @return void
     */
    public function setPageTitlecontainer($pageTitlecontainer): void
    {
        $this->pageTitlecontainer = $pageTitlecontainer;
    }

    /**
     * Returns the pageTitleclass
     *
     * @return string $pageTitleclass
     */
    public function getPageTitleclass()
    {
        return $this->pageTitleclass;
    }

    /**
     * Sets the pageTitleclass
     *
     * @param string $pageTitleclass
     * @return void
     */
    public function setPageTitleclass($pageTitleclass): void
    {
        $this->pageTitleclass = $pageTitleclass;
    }

    /**
     * Returns the metaEnable
     *
     * @return string $metaEnable
     */
    public function getMetaEnable()
    {
        return $this->metaEnable;
    }

    /**
     * Sets the metaEnable
     *
     * @param string $metaEnable
     * @return void
     */
    public function setMetaEnable($metaEnable): void
    {
        $this->metaEnable = $metaEnable;
    }

    /**
     * Returns the metaValue
     *
     * @return string $metaValue
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * Sets the metaValue
     *
     * @param string $metaValue
     * @return void
     */
    public function setMetaValue($metaValue): void
    {
        $this->metaValue = $metaValue;
    }

    /**
     * Returns the metaContainer
     *
     * @return string $metaContainer
     */
    public function getMetaContainer()
    {
        return $this->metaContainer;
    }

    /**
     * Sets the metaContainer
     *
     * @param string $metaContainer
     * @return void
     */
    public function setMetaContainer($metaContainer): void
    {
        $this->metaContainer = $metaContainer;
    }

    /**
     * Returns the metaClass
     *
     * @return string $metaClass
     */
    public function getMetaClass()
    {
        return $this->metaClass;
    }

    /**
     * Sets the metaClass
     *
     * @param string $metaClass
     * @return void
     */
    public function setMetaClass($metaClass): void
    {
        $this->metaClass = $metaClass;
    }

    /**
     * Returns the metaText
     *
     * @return string $metaText
     */
    public function getMetaText()
    {
        return $this->metaText;
    }

    /**
     * Sets the metaText
     *
     * @param string $metaText
     * @return void
     */
    public function setMetaText($metaText): void
    {
        $this->metaText = $metaText;
    }

    /**
     * Returns the navbarEnable
     *
     * @return string $navbarEnable
     */
    public function getNavbarEnable()
    {
        return $this->navbarEnable;
    }

    /**
     * Sets the navbarEnable
     *
     * @param string $navbarEnable
     * @return void
     */
    public function setNavbarEnable($navbarEnable): void
    {
        $this->navbarEnable = $navbarEnable;
    }

    /**
     * Returns the navbarEntrylevel
     *
     * @return int $navbarEntrylevel
     */
    public function getNavbarEntrylevel()
    {
        return $this->navbarEntrylevel;
    }

    /**
     * Sets the navbarEntrylevel
     *
     * @param int $navbarEntrylevel
     * @return void
     */
    public function setNavbarEntrylevel($navbarEntrylevel): void
    {
        $this->navbarEntrylevel = $navbarEntrylevel;
    }

    /**
     * Returns the navbarLevels
     *
     * @return int $navbarLevels
     */
    public function getNavbarLevels()
    {
        return $this->navbarLevels;
    }

    /**
     * Sets the navbarLevels
     *
     * @param int $navbarLevels
     * @return void
     */
    public function setNavbarLevels($navbarLevels): void
    {
        $this->navbarLevels = $navbarLevels;
    }

    /**
     * Returns the navbarExcludeuiduist
     *
     * @return string $navbarExcludeuiduist
     */
    public function getNavbarExcludeuiduist()
    {
        return $this->navbarExcludeuiduist;
    }

    /**
     * Sets the navbarExcludeuiduist
     *
     * @param string $navbarExcludeuiduist
     * @return void
     */
    public function setNavbarExcludeuiduist($navbarExcludeuiduist): void
    {
        $this->navbarExcludeuiduist = $navbarExcludeuiduist;
    }

    /**
     * Returns the navbarSectionmenu
     *
     * @return bool $navbarSectionmenu
     */
    public function getNavbarSectionmenu()
    {
        return $this->navbarSectionmenu;
    }

    /**
     * Sets the navbarSectionmenu
     *
     * @param bool $navbarSectionmenu
     * @return void
     */
    public function setNavbarSectionmenu($navbarSectionmenu): void
    {
        $this->navbarSectionmenu = $navbarSectionmenu;
    }

    /**
     * Returns the boolean state of navbarSectionmenu
     *
     * @return bool
     */
    public function isNavbarSectionmenu()
    {
        return $this->navbarSectionmenu;
    }

    /**
     * Returns the navbarMegamenu
     *
     * @return bool $navbarMegamenu
     */
    public function getNavbarMegamenu()
    {
        return $this->navbarMegamenu;
    }

    /**
     * Sets the navbarMegamenu
     *
     * @param bool $navbarMegamenu
     * @return void
     */
    public function setNavbarMegamenu($navbarMegamenu): void
    {
        $this->navbarMegamenu = $navbarMegamenu;
    }

    /**
     * Returns the boolean state of navbarMegamenu
     *
     * @return bool
     */
    public function isNavbarMegamenu()
    {
        return $this->navbarMegamenu;
    }

    /**
     * Returns the navbarHover
     *
     * @return bool $navbarHover
     */
    public function getNavbarHover()
    {
        return $this->navbarHover;
    }

    /**
     * Sets the navbarHover
     *
     * @param bool $navbarHover
     * @return void
     */
    public function setNavbarHover($navbarHover): void
    {
        $this->navbarHover = $navbarHover;
    }

    /**
     * Returns the boolean state of navbarHover
     *
     * @return bool
     */
    public function isNavbarHover()
    {
        return $this->navbarHover;
    }

    /**
     * Returns the navbarClickableparent
     *
     * @return bool $navbarClickableparent
     */
    public function getNavbarClickableparent()
    {
        return $this->navbarClickableparent;
    }

    /**
     * Sets the navbarClickableparent
     *
     * @param bool $navbarClickableparent
     * @return void
     */
    public function setNavbarClickableparent($navbarClickableparent): void
    {
        $this->navbarClickableparent = $navbarClickableparent;
    }

    /**
     * Returns the navbarPlusicon
     *
     * @return bool $navbarPlusicon
     */
    public function getNavbarPlusicon()
    {
        return $this->navbarPlusicon;
    }

    /**
     * Sets the navbarPlusicon
     *
     * @param bool $navbarPlusicon
     * @return void
     */
    public function setNavbarPlusicon($navbarPlusicon): void
    {
        $this->navbarPlusicon = $navbarPlusicon;
    }

    /**
     * Returns the navbarDropdownAnimate
     *
     * @return string $navbarDropdownAnimate
     */
    public function getNavbarDropdownAnimate()
    {
        return $this->navbarDropdownAnimate;
    }

    /**
     * Sets the navbarDropdownAnimate
     *
     * @param string $navbarDropdownAnimate
     * @return void
     */
    public function setNavbarDropdownAnimate($navbarDropdownAnimate): void
    {
        $this->navbarDropdownAnimate = $navbarDropdownAnimate;
    }

    /**
     * Returns the boolean state of navbarClickableparent
     *
     * @return bool
     */
    public function isNavbarClickableparent()
    {
        return $this->navbarClickableparent;
    }

    /**
     * Returns the navbarIncludespacer
     *
     * @return bool $navbarIncludespacer
     */
    public function getNavbarIncludespacer()
    {
        return $this->navbarIncludespacer;
    }

    /**
     * Sets the navbarIncludespacer
     *
     * @param bool $navbarIncludespacer
     * @return void
     */
    public function setNavbarIncludespacer($navbarIncludespacer): void
    {
        $this->navbarIncludespacer = $navbarIncludespacer;
    }

    /**
     * Returns the boolean state of navbarIncludespacer
     *
     * @return bool
     */
    public function isNavbarIncludespacer()
    {
        return $this->navbarIncludespacer;
    }

    /**
     * Returns the navbarBrand
     *
     * @return string $navbarBrand
     */
    public function getNavbarBrand()
    {
        return $this->navbarBrand;
    }

    /**
     * Sets the navbarBrand
     *
     * @param string $navbarBrand
     * @return void
     */
    public function setNavbarBrand($navbarBrand): void
    {
        $this->navbarBrand = $navbarBrand;
    }

    /**
     * Returns the navbarbrandAlignment
     *
     * @return string $navbarbrandAlignment
     */
    public function getNavbarbrandAlignment()
    {
        return $this->navbarbrandAlignment;
    }

    /**
     * Sets the navbarbrandAlignment
     *
     * @param string $navbarbrandAlignment
     * @return void
     */
    public function setNavbarbrandAlignment($navbarbrandAlignment): void
    {
        $this->navbarbrandAlignment = $navbarbrandAlignment;
    }

    /**
     * Returns the navbarImage
     *
     * @return string $navbarImage
     */
    public function getNavbarImage()
    {
        return $this->navbarImage;
    }

    /**
     * Sets the navbarImage
     *
     * @param string $navbarImage
     * @return void
     */
    public function setNavbarImage($navbarImage): void
    {
        $this->navbarImage = $navbarImage;
    }

    /**
     * Returns the navbarColor
     *
     * @return string $navbarColor
     */
    public function getNavbarColor()
    {
        return $this->navbarColor;
    }

    /**
     * Sets the navbarColor
     *
     * @param string $navbarColor
     * @return void
     */
    public function setNavbarColor($navbarColor): void
    {
        $this->navbarColor = $navbarColor;
    }

    /**
     * Returns the navbarBackground
     *
     * @return string $navbarBackground
     */
    public function getNavbarBackground()
    {
        return $this->navbarBackground;
    }

    /**
     * Returns the navbarShrinkcolorschemes
     *
     * @return string $navbarShrinkcolorschemes
     */
    public function getNavbarShrinkcolorschemes()
    {
        return $this->navbarShrinkcolorschemes;
    }

    /**
     * Sets the navbarShrinkcolorschemes
     *
     * @param string $navbarShrinkcolorschemes
     * @return void
     */
    public function setNavbarShrinkcolorschemes($navbarShrinkcolorschemes): void
    {
        $this->navbarShrinkcolorschemes = $navbarShrinkcolorschemes;
    }

    /**
     * Returns the navbarShrinkcolor
     *
     * @return string $navbarShrinkcolor
     */
    public function getNavbarShrinkcolor()
    {
        return $this->navbarShrinkcolor;
    }

    /**
     * Sets the navbarShrinkcolor
     *
     * @param string $navbarShrinkcolor
     * @return void
     */
    public function setNavbarShrinkcolor($navbarShrinkcolor): void
    {
        $this->navbarShrinkcolor = $navbarShrinkcolor;
    }

    /**
     * Sets the navbarBackground
     *
     * @param string $navbarBackground
     * @return void
     */
    public function setNavbarBackground($navbarBackground): void
    {
        $this->navbarBackground = $navbarBackground;
    }

    /**
     * Returns the navbarContainer
     *
     * @return string $navbarContainer
     */
    public function getNavbarContainer()
    {
        return $this->navbarContainer;
    }

    /**
     * Sets the navbarContainer
     *
     * @param string $navbarContainer
     * @return void
     */
    public function setNavbarContainer($navbarContainer): void
    {
        $this->navbarContainer = $navbarContainer;
    }

    /**
     * Returns the navbarInnercontainer
     *
     * @return string $navbarInnercontainer
     */
    public function getNavbarInnercontainer()
    {
        return $this->navbarInnercontainer;
    }

    /**
     * Sets the navbarInnercontainer
     *
     * @param string $navbarInnercontainer
     * @return void
     */
    public function setNavbarInnercontainer($navbarInnercontainer): void
    {
        $this->navbarInnercontainer = $navbarInnercontainer;
    }

    /**
     * Returns the navbarPlacement
     *
     * @return string $navbarPlacement
     */
    public function getNavbarPlacement()
    {
        return $this->navbarPlacement;
    }

    /**
     * Sets the navbarPlacement
     *
     * @param string $navbarPlacement
     * @return void
     */
    public function setNavbarPlacement($navbarPlacement): void
    {
        $this->navbarPlacement = $navbarPlacement;
    }

    /**
     * Returns the navbarAlignment
     *
     * @return string $navbarAlignment
     */
    public function getNavbarAlignment()
    {
        return $this->navbarAlignment;
    }

    /**
     * Sets the navbarAlignment
     *
     * @param string $navbarAlignment
     * @return void
     */
    public function setNavbarAlignment($navbarAlignment): void
    {
        $this->navbarAlignment = $navbarAlignment;
    }

    /**
     * Returns the navbarClass
     *
     * @return string $navbarClass
     */
    public function getNavbarClass()
    {
        return $this->navbarClass;
    }

    /**
     * Sets the navbarClass
     *
     * @param string $navbarClass
     * @return void
     */
    public function setNavbarClass($navbarClass): void
    {
        $this->navbarClass = $navbarClass;
    }

    /**
     * Returns the navbarToggler
     *
     * @return string $navbarToggler
     */
    public function getNavbarToggler()
    {
        return $this->navbarToggler;
    }

    /**
     * Sets the navbarToggler
     *
     * @param string $navbarToggler
     * @return void
     */
    public function setNavbarToggler($navbarToggler): void
    {
        $this->navbarToggler = $navbarToggler;
    }

    /**
     * Returns the navbarBreakpoint
     *
     * @return string $navbarBreakpoint
     */
    public function getNavbarBreakpoint()
    {
        return $this->navbarBreakpoint;
    }

    /**
     * Sets the navbarBreakpoint
     *
     * @param string $navbarBreakpoint
     * @return void
     */
    public function setNavbarBreakpoint($navbarBreakpoint): void
    {
        $this->navbarBreakpoint = $navbarBreakpoint;
    }

    /**
     * Returns the navbarOffcanvas
     *
     * @return bool $navbarOffcanvas
     */
    public function getNavbarOffcanvas()
    {
        return $this->navbarOffcanvas;
    }

    /**
     * Sets the navbarOffcanvas
     *
     * @param bool $navbarOffcanvas
     * @return void
     */
    public function setNavbarOffcanvas($navbarOffcanvas): void
    {
        $this->navbarOffcanvas = $navbarOffcanvas;
    }

    /**
     * Returns the boolean state of navbarOffcanvas
     *
     * @return bool
     */
    public function isNavbarOffcanvas()
    {
        return $this->navbarOffcanvas;
    }

    /**
     * Returns the navbarAnimatedtoggler
     *
     * @return bool $navbarAnimatedtoggler
     */
    public function getNavbarAnimatedtoggler()
    {
        return $this->navbarAnimatedtoggler;
    }

    /**
     * Sets the navbarAnimatedtoggler
     *
     * @param bool $navbarAnimatedtoggler
     * @return void
     */
    public function setNavbarAnimatedtoggler($navbarAnimatedtoggler): void
    {
        $this->navbarAnimatedtoggler = $navbarAnimatedtoggler;
    }

    /**
     * Returns the boolean state of navbarAnimatedtoggler
     *
     * @return bool
     */
    public function isNavbarAnimatedtoggler()
    {
        return $this->navbarAnimatedtoggler;
    }

    /**
     * Returns the navbarTransparent
     *
     * @return bool $navbarTransparent
     */
    public function getNavbarTransparent()
    {
        return $this->navbarTransparent;
    }

    /**
     * Sets the navbarTransparent
     *
     * @param bool $navbarTransparent
     * @return void
     */
    public function setNavbarTransparent($navbarTransparent): void
    {
        $this->navbarTransparent = $navbarTransparent;
    }

    /**
     * Returns the boolean state of navbarTransparent
     *
     * @return bool
     */
    public function isNavbarTransparent()
    {
        return $this->navbarTransparent;
    }

    /**
     * Returns the navbarHeight
     *
     * @return int $navbarHeight
     */
    public function getNavbarHeight()
    {
        return $this->navbarHeight;
    }

    /**
     * Sets the navbarHeight
     *
     * @param int $navbarHeight
     * @return void
     */
    public function setNavbarHeight($navbarHeight): void
    {
        $this->navbarHeight = $navbarHeight;
    }

    /**
     * Returns the navbarSearchbox
     *
     * @return string $navbarSearchbox
     */
    public function getNavbarSearchbox()
    {
        return $this->navbarSearchbox;
    }

    /**
     * Sets the navbarSearchbox
     *
     * @param string $navbarSearchbox
     * @return void
     */
    public function setNavbarSearchbox($navbarSearchbox): void
    {
        $this->navbarSearchbox = $navbarSearchbox;
    }

    /**
     * Returns the navbarLangmenu
     *
     * @return bool $navbarLangmenu
     */
    public function getNavbarLangmenu()
    {
        return $this->navbarLangmenu;
    }

    /**
     * Sets the navbarLangmenu
     *
     * @param bool $navbarLangmenu
     * @return void
     */
    public function setNavbarLangmenu($navbarLangmenu): void
    {
        $this->navbarLangmenu = $navbarLangmenu;
    }

    /**
     * Returns the boolean state of navbarLangmenu
     *
     * @return bool
     */
    public function isNavbarLangmenu()
    {
        return $this->navbarLangmenu;
    }

    /**
     * Returns the navbarRightMenuUidList
     *
     * @return string $navbarRightMenuUidList
     */
    public function getNavbarRightMenuUidList()
    {
        return $this->navbarRightMenuUidList;
    }

    /**
     * Sets the navbarRightMenuUidList
     *
     * @param string $navbarRightMenuUidList
     * @return void
     */
    public function setNavbarRightMenuUidList($navbarRightMenuUidList): void
    {
        $this->navbarRightMenuUidList = $navbarRightMenuUidList;
    }

    /**
     * Sets the navbarExtraRow
     *
     * @param string $navbarExtraRow
     * @return void
     */
    public function setNavbarExtraRow($navbarExtraRow): void
    {
        $this->navbarExtraRow = $navbarExtraRow;
    }

    /**
     * Returns the navbarExtraRow
     *
     * @return bool $navbarExtraRow
     */
    public function getNavbarExtraRow()
    {
        return $this->navbarExtraRow;
    }

    /**
     * Sets the navbarLangFlags
     *
     * @param string $navbarLangFlags
     * @return void
     */
    public function setNavbarLangFlags($navbarLangFlags): void
    {
        $this->navbarLangFlags = $navbarLangFlags;
    }

    /**
     * Returns the navbarLangFlags
     *
     * @return bool $navbarLangFlags
     */
    public function getNavbarLangFlags()
    {
        return $this->navbarLangFlags;
    }

    /**
     * Returns the jumbotronEnable
     *
     * @return bool $jumbotronEnable
     */
    public function getJumbotronEnable()
    {
        return $this->jumbotronEnable;
    }

    /**
     * Returns the jumbotronRounded
     *
     * @return bool $jumbotronRounded
     */
    public function getJumbotronRounded()
    {
        return $this->jumbotronRounded;
    }

    /**
     * Sets the jumbotronRounded
     *
     * @param bool $jumbotronRounded
     * @return void
     */
    public function setJumbotronRounded($jumbotronRounded): void
    {
        $this->jumbotronRounded = $jumbotronRounded;
    }

    /**
     * Sets the jumbotronEnable
     *
     * @param bool $jumbotronEnable
     * @return void
     */
    public function setJumbotronEnable($jumbotronEnable): void
    {
        $this->jumbotronEnable = $jumbotronEnable;
    }

    /**
     * Returns the boolean state of jumbotronEnable
     *
     * @return bool
     */
    public function isJumbotronEnable()
    {
        return $this->jumbotronEnable;
    }

    /**
     * Returns the jumbotronBgimage
     *
     * @return string $jumbotronBgimage
     */
    public function getJumbotronBgimage()
    {
        return $this->jumbotronBgimage;
    }

    /**
     * Sets the jumbotronBgimage
     *
     * @param string $jumbotronBgimage
     * @return void
     */
    public function setJumbotronBgimage($jumbotronBgimage): void
    {
        $this->jumbotronBgimage = $jumbotronBgimage;
    }

    /**
     * Returns the jumbotronBgimageratio
     *
     * @return string $jumbotronBgimageratio
     */
    public function getJumbotronBgimageratio()
    {
        return $this->jumbotronBgimageratio;
    }

    /**
     * Sets the jumbotronBgimageratio
     *
     * @param string $jumbotronBgimageratio
     * @return void
     */
    public function setJumbotronBgimageratio($jumbotronBgimageratio): void
    {
        $this->jumbotronBgimageratio = $jumbotronBgimageratio;
    }

    /**
     * Returns the jumbotronAlignitem
     *
     * @return string $jumbotronAlignItem
     */
    public function getJumbotronAlignItem()
    {
        return $this->jumbotronAlignitem;
    }

    /**
     * Sets the jumbotronAlignitem
     *
     * @param string $jumbotronAlignitem
     * @return void
     */
    public function setJumbotronAlignitem($jumbotronAlignitem): void
    {
        $this->jumbotronAlignitem = $jumbotronAlignitem;
    }

    /**
     * Returns the jumbotronSlide
     *
     * @return bool $jumbotronSlide
     */
    public function getJumbotronSlide()
    {
        return $this->jumbotronSlide;
    }

    /**
     * Sets the jumbotronSlide
     *
     * @param bool $jumbotronSlide
     * @return void
     */
    public function setJumbotronSlide($jumbotronSlide): void
    {
        $this->jumbotronSlide = $jumbotronSlide;
    }

    /**
     * Returns the boolean state of jumbotronSlide
     *
     * @return bool
     */
    public function isJumbotronSlide()
    {
        return $this->jumbotronSlide;
    }

    /**
     * Returns the jumbotronPosition
     *
     * @return string $jumbotronPosition
     */
    public function getJumbotronPosition()
    {
        return $this->jumbotronPosition;
    }

    /**
     * Sets the jumbotronPosition
     *
     * @param string $jumbotronPosition
     * @return void
     */
    public function setJumbotronPosition($jumbotronPosition): void
    {
        $this->jumbotronPosition = $jumbotronPosition;
    }

    /**
     * Returns the jumbotronContainer
     *
     * @return string $jumbotronContainer
     */
    public function getJumbotronContainer()
    {
        return $this->jumbotronContainer;
    }

    /**
     * Sets the jumbotronContainer
     *
     * @param string $jumbotronContainer
     * @return void
     */
    public function setJumbotronContainer($jumbotronContainer): void
    {
        $this->jumbotronContainer = $jumbotronContainer;
    }

    /**
     * Returns the jumbotronContainerposition
     *
     * @return string $jumbotronContainerposition
     */
    public function getJumbotronContainerposition()
    {
        return $this->jumbotronContainerposition;
    }

    /**
     * Sets the jumbotronContainerposition
     *
     * @param string $jumbotronContainerposition
     * @return void
     */
    public function setJumbotronContainerposition($jumbotronContainerposition): void
    {
        $this->jumbotronContainerposition = $jumbotronContainerposition;
    }

    /**
     * Returns the jumbotronClass
     *
     * @return string $jumbotronClass
     */
    public function getJumbotronClass()
    {
        return $this->jumbotronClass;
    }

    /**
     * Sets the jumbotronClass
     *
     * @param string $jumbotronClass
     * @return void
     */
    public function setJumbotronClass($jumbotronClass): void
    {
        $this->jumbotronClass = $jumbotronClass;
    }

    /**
     * Returns the jumbotronCarouselInterval
     *
     * @return int $jumbotronCarouselInterval
     */
    public function getJumbotronCarouselInterval()
    {
        return $this->jumbotronCarouselInterval;
    }

    /**
     * Sets the jumbotronCarouselInterval
     *
     * @param int $jumbotronCarouselInterval
     * @return void
     */
    public function setJumbotronCarouselInterval($jumbotronCarouselInterval): void
    {
        $this->jumbotronCarouselInterval = $jumbotronCarouselInterval;
    }

    /**
     * Returns the jumbotronCarouselPause
     *
     * @return bool $jumbotronCarouselPause
     */
    public function getJumbotronCarouselPause()
    {
        return $this->jumbotronCarouselPause;
    }

    /**
     * Sets the jumbotronCarouselPause
     *
     * @param bool $jumbotronCarouselPause
     * @return void
     */
    public function setJumbotronCarouselPause($jumbotronCarouselPause): void
    {
        $this->jumbotronCarouselPause = $jumbotronCarouselPause;
    }

    /**
     * Returns the breadcrumbEnable
     *
     * @return bool $breadcrumbEnable
     */
    public function getBreadcrumbEnable()
    {
        return $this->breadcrumbEnable;
    }

    /**
     * Sets the breadcrumbEnable
     *
     * @param bool $breadcrumbEnable
     * @return void
     */
    public function setBreadcrumbEnable($breadcrumbEnable): void
    {
        $this->breadcrumbEnable = $breadcrumbEnable;
    }

    /**
     * Returns the boolean state of breadcrumbEnable
     *
     * @return bool
     */
    public function isBreadcrumbEnable()
    {
        return $this->breadcrumbEnable;
    }

    /**
     * Returns the breadcrumbNotonrootpage
     *
     * @return bool $breadcrumbNotonrootpage
     */
    public function getBreadcrumbNotonrootpage()
    {
        return $this->breadcrumbNotonrootpage;
    }

    /**
     * Sets the breadcrumbNotonrootpage
     *
     * @param bool $breadcrumbNotonrootpage
     * @return void
     */
    public function setBreadcrumbNotonrootpage($breadcrumbNotonrootpage): void
    {
        $this->breadcrumbNotonrootpage = $breadcrumbNotonrootpage;
    }

    /**
     * Returns the boolean state of breadcrumbNotonrootpage
     *
     * @return bool
     */
    public function isBreadcrumbNotonrootpage()
    {
        return $this->breadcrumbNotonrootpage;
    }

    /**
     * Returns the breadcrumbFaicon
     *
     * @return bool $breadcrumbFaicon
     */
    public function getBreadcrumbFaicon()
    {
        return $this->breadcrumbFaicon;
    }

    /**
     * Sets the breadcrumbFaicon
     *
     * @param bool $breadcrumbFaicon
     * @return void
     */
    public function setBreadcrumbFaicon($breadcrumbFaicon): void
    {
        $this->breadcrumbFaicon = $breadcrumbFaicon;
    }

    /**
     * Returns the boolean state of breadcrumbNotonrootpage
     *
     * @return bool
     */
    public function isBreadcrumbFaicon()
    {
        return $this->breadcrumbFaicon;
    }

    /**
     * Returns the breadcrumbFaicononly
     *
     * @return bool $breadcrumbFaicononly
     */
    public function getBreadcrumbFaicononly()
    {
        return $this->breadcrumbFaicononly;
    }

    /**
     * Sets the breadcrumbFaicononly
     *
     * @param bool $breadcrumbFaicononly
     * @return void
     */
    public function setBreadcrumbFaicononly($breadcrumbFaicononly): void
    {
        $this->breadcrumbFaicononly = $breadcrumbFaicononly;
    }

    /**
     * Returns the boolean state of isBreadcrumbFaicononly
     *
     * @return bool
     */
    public function isBreadcrumbFaicononly()
    {
        return $this->breadcrumbFaicononly;
    }

    /**
     * Returns the breadcrumbCorner
     *
     * @return bool $breadcrumbCorner
     */
    public function getBreadcrumbCorner()
    {
        return $this->breadcrumbCorner;
    }

    /**
     * Sets the breadcrumbCorner
     *
     * @param bool $breadcrumbCorner
     * @return void
     */
    public function setBreadcrumbCorner($breadcrumbCorner): void
    {
        $this->breadcrumbCorner = $breadcrumbCorner;
    }

    /**
     * Returns the boolean state of breadcrumbCorner
     *
     * @return bool
     */
    public function isBreadcrumbCorner()
    {
        return $this->breadcrumbCorner;
    }

    /**
     * Returns the breadcrumbBottom
     *
     * @return bool $breadcrumbBottom
     */
    public function getBreadcrumbBottom()
    {
        return $this->breadcrumbBottom;
    }

    /**
     * Sets the breadcrumbBottom
     *
     * @param bool $breadcrumbBottom
     * @return void
     */
    public function setBreadcrumbBottom($breadcrumbBottom): void
    {
        $this->breadcrumbBottom = $breadcrumbBottom;
    }

    /**
     * Returns the boolean state of breadcrumbBottom
     *
     * @return bool
     */
    public function isBreadcrumbBottom()
    {
        return $this->breadcrumbBottom;
    }

    /**
     * Returns the breadcrumbPosition
     *
     * @return string $breadcrumbPosition
     */
    public function getBreadcrumbPosition()
    {
        return $this->breadcrumbPosition;
    }

    /**
     * Sets the breadcrumbPosition
     *
     * @param string $breadcrumbPosition
     * @return void
     */
    public function setBreadcrumbPosition($breadcrumbPosition): void
    {
        $this->breadcrumbPosition = $breadcrumbPosition;
    }

    /**
     * Returns the breadcrumbContainer
     *
     * @return string $breadcrumbContainer
     */
    public function getBreadcrumbContainer()
    {
        return $this->breadcrumbContainer;
    }

    /**
     * Sets the breadcrumbContainer
     *
     * @param string $breadcrumbContainer
     * @return void
     */
    public function setBreadcrumbContainer($breadcrumbContainer): void
    {
        $this->breadcrumbContainer = $breadcrumbContainer;
    }

    /**
     * Returns the breadcrumbContainerposition
     *
     * @return string $breadcrumbContainerposition
     */
    public function getBreadcrumbContainerposition()
    {
        return $this->breadcrumbContainerposition;
    }

    /**
     * Sets the breadcrumbContainerposition
     *
     * @param string $breadcrumbContainerposition
     * @return void
     */
    public function setBreadcrumbContainerposition($breadcrumbContainerposition): void
    {
        $this->breadcrumbContainerposition = $breadcrumbContainerposition;
    }

    /**
     * Returns the breadcrumbClass
     *
     * @return string $breadcrumbClass
     */
    public function getBreadcrumbClass()
    {
        return $this->breadcrumbClass;
    }

    /**
     * Sets the breadcrumbClass
     *
     * @param string $breadcrumbClass
     * @return void
     */
    public function setBreadcrumbClass($breadcrumbClass): void
    {
        $this->breadcrumbClass = $breadcrumbClass;
    }

    /**
     * Returns the sidebarEnable
     *
     * @return string $sidebarEnable
     */
    public function getSidebarEnable()
    {
        return $this->sidebarEnable;
    }

    /**
     * Sets the sidebarEnable
     *
     * @param string $sidebarEnable
     * @return void
     */
    public function setSidebarEnable($sidebarEnable): void
    {
        $this->sidebarEnable = $sidebarEnable;
    }

    /**
     * Returns the sidebarRightenable
     *
     * @return string $sidebarRightenable
     */
    public function getSidebarRightenable()
    {
        return $this->sidebarRightenable;
    }

    /**
     * Sets the sidebarRightenable
     *
     * @param string $sidebarRightenable
     * @return void
     */
    public function setSidebarRightenable($sidebarRightenable): void
    {
        $this->sidebarRightenable = $sidebarRightenable;
    }

    /**
     * Returns the sidebarEntrylevel
     *
     * @return int $sidebarEntrylevel
     */
    public function getSidebarEntrylevel()
    {
        return $this->sidebarEntrylevel;
    }

    /**
     * Sets the sidebarEntrylevel
     *
     * @param int $sidebarEntrylevel
     * @return void
     */
    public function setSidebarEntrylevel($sidebarEntrylevel): void
    {
        $this->sidebarEntrylevel = $sidebarEntrylevel;
    }

    /**
     * Returns the sidebarLevels
     *
     * @return int $sidebarLevels
     */
    public function getSidebarLevels()
    {
        return $this->sidebarLevels;
    }

    /**
     * Sets the sidebarLevels
     *
     * @param int $sidebarLevels
     * @return void
     */
    public function setSidebarLevels($sidebarLevels): void
    {
        $this->sidebarLevels = $sidebarLevels;
    }

    /**
     * Returns the sidebarExcludeuiduist
     *
     * @return string $sidebarExcludeuiduist
     */
    public function getSidebarExcludeuiduist()
    {
        return $this->sidebarExcludeuiduist;
    }

    /**
     * Sets the sidebarExcludeuiduist
     *
     * @param string $sidebarExcludeuiduist
     * @return void
     */
    public function setSidebarExcludeuiduist($sidebarExcludeuiduist): void
    {
        $this->sidebarExcludeuiduist = $sidebarExcludeuiduist;
    }

    /**
     * Returns the sidebarIncludespacer
     *
     * @return bool $sidebarIncludespacer
     */
    public function getSidebarIncludespacer()
    {
        return $this->sidebarIncludespacer;
    }

    /**
     * Sets the sidebarIncludespacer
     *
     * @param bool $sidebarIncludespacer
     * @return void
     */
    public function setSidebarIncludespacer($sidebarIncludespacer): void
    {
        $this->sidebarIncludespacer = $sidebarIncludespacer;
    }

    /**
     * Returns the boolean state of sidebarIncludespacer
     *
     * @return bool
     */
    public function isSidebarIncludespacer()
    {
        return $this->sidebarIncludespacer;
    }

    /**
     * Returns the footerEnable
     *
     * @return bool $footerEnable
     */
    public function getFooterEnable()
    {
        return $this->footerEnable;
    }

    /**
     * Sets the footerEnable
     *
     * @param bool $footerEnable
     * @return void
     */
    public function setFooterEnable($footerEnable): void
    {
        $this->footerEnable = $footerEnable;
    }

    /**
     * Returns the boolean state of footerEnable
     *
     * @return bool
     */
    public function isFooterEnable()
    {
        return $this->footerEnable;
    }

    /**
     * Returns the footerFluid
     *
     * @return bool $footerFluid
    public function getFooterFluid()
    {
        return $this->footerFluid;
    }
     */

    /**
     * Sets the footerFluid
     *
     * @param bool $footerFluid
     * @return void
    public function setFooterFluid($footerFluid)
    {
        $this->footerFluid = $footerFluid;
    }
     */

    /**
     * Returns the boolean state of footerFluid
     *
     * @return bool
    public function isFooterFluid()
    {
        return $this->footerFluid;
    }
     */

    /**
     * Returns the footerSlide
     *
     * @return bool $footerSlide
     */
    public function getFooterSlide()
    {
        return $this->footerSlide;
    }

    /**
     * Sets the footerSlide
     *
     * @param bool $footerSlide
     * @return void
     */
    public function setFooterSlide($footerSlide): void
    {
        $this->footerSlide = $footerSlide;
    }

    /**
     * Returns the boolean state of footerSlide
     *
     * @return bool
     */
    public function isFooterSlide()
    {
        return $this->footerSlide;
    }

    /**
     * Returns the footerSticky
     *
     * @return bool $footerSticky
     */
    public function getFooterSticky()
    {
        return $this->footerSticky;
    }

    /**
     * Sets the footerSticky
     *
     * @param bool $footerSticky
     * @return void
     */
    public function setFooterSticky($footerSticky): void
    {
        $this->footerSticky = $footerSticky;
    }

    /**
     * Returns the boolean state of footerSticky
     *
     * @return bool
     */
    public function isFooterSticky()
    {
        return $this->footerSticky;
    }

    /**
     * Returns the footerContainer
     *
     * @return string $footerContainer
     */
    public function getFooterContainer()
    {
        return $this->footerContainer;
    }

    /**
     * Sets the footerContainer
     *
     * @param string $footerContainer
     * @return void
     */
    public function setFooterContainer($footerContainer): void
    {
        $this->footerContainer = $footerContainer;
    }

    /**
     * Returns the footerContainerposition
     *
     * @return string $footerContainerposition
     */
    public function getFooterContainerposition()
    {
        return $this->footerContainerposition;
    }

    /**
     * Sets the footerContainerposition
     *
     * @param string $footerContainerposition
     * @return void
     */
    public function setFooterContainerposition($footerContainerposition): void
    {
        $this->footerContainerposition = $footerContainerposition;
    }

    /**
     * Returns the footerClass
     *
     * @return string $footerClass
     */
    public function getFooterClass()
    {
        return $this->footerClass;
    }

    /**
     * Sets the footerClass
     *
     * @param string $footerClass
     * @return void
     */
    public function setFooterClass($footerClass): void
    {
        $this->footerClass = $footerClass;
    }

    /**
     * Returns the footerPid
     *
     * @return int $footerPid
     */
    public function getFooterPid()
    {
        return $this->footerPid;
    }

    /**
     * Sets the footerPid
     *
     * @param int $footerPid
     * @return void
     */
    public function setFooterPid($footerPid): void
    {
        $this->footerPid = $footerPid;
    }


    /**
     * Returns the expandedcontentEnabletop
     *
     * @return bool $expandedcontentEnabletop
     */
    public function getexpandedcontentEnabletop()
    {
        return $this->expandedcontentEnabletop;
    }

    /**
     * Sets the expandedcontentEnabletop
     *
     * @param bool $expandedcontentEnabletop
     * @return void
     */
    public function setexpandedcontentEnabletop($expandedcontentEnabletop): void
    {
        $this->expandedcontentEnabletop = $expandedcontentEnabletop;
    }

    /**
     * Returns the boolean state of expandedcontentEnabletop
     *
     * @return bool
     */
    public function isexpandedcontentEnabletop()
    {
        return $this->expandedcontentEnabletop;
    }

    /**
     * Returns the expandedcontentSlidetop
     *
     * @return bool $expandedcontentSlidetop
     */
    public function getExpandedcontentSlidetop()
    {
        return $this->expandedcontentSlidetop;
    }

    /**
     * Sets the expandedcontentSlidetop
     *
     * @param bool $expandedcontentSlidetop
     * @return void
     */
    public function setExpandedcontentSlidetop($expandedcontentSlidetop): void
    {
        $this->expandedcontentSlidetop = $expandedcontentSlidetop;
    }

    /**
     * Returns the boolean state of expandedcontentSlidetop
     *
     * @return bool
     */
    public function isExpandedcontentSlidetop()
    {
        return $this->expandedcontentSlidetop;
    }

    /**
     * Returns the expandedcontentContainerpositiontop
     *
     * @return string $expandedcontentContainerpositiontop
     */
    public function getExpandedcontentContainerpositiontop()
    {
        return $this->expandedcontentContainerpositiontop;
    }

    /**
     * Sets the expandedcontentContainerpositiontop
     *
     * @param string $expandedcontentContainerpositiontop
     * @return void
     */
    public function setExpandedcontentContainerpositiontop($expandedcontentContainerpositiontop): void
    {
        $this->expandedcontentContainerpositiontop = $expandedcontentContainerpositiontop;
    }

    /**
     * Returns the expandedcontentContainertop
     *
     * @return string $expandedcontentContainertop
     */
    public function getExpandedcontentContainertop()
    {
        return $this->expandedcontentContainertop;
    }

    /**
     * Sets the expandedcontentContainertop
     *
     * @param string $expandedcontentContainertop
     * @return void
     */
    public function setExpandedcontentContainertop($expandedcontentContainertop): void
    {
        $this->expandedcontentContainertop = $expandedcontentContainertop;
    }

    /**
     * Returns the expandedcontentClasstop
     *
     * @return string $expandedcontentClasstop
     */
    public function getExpandedcontentClasstop()
    {
        return $this->expandedcontentClasstop;
    }

    /**
     * Sets the expandedcontentClasstop
     *
     * @param string $expandedcontentClasstop
     * @return void
     */
    public function setExpandedcontentClasstop($expandedcontentClasstop): void
    {
        $this->expandedcontentClasstop = $expandedcontentClasstop;
    }

    /**
     * Returns the expandedcontentEnablebottom
     *
     * @return bool $expandedcontentEnablebottom
     */
    public function getExpandedcontentEnablebottom()
    {
        return $this->expandedcontentEnablebottom;
    }

    /**
     * Sets the expandedcontentEnablebottom
     *
     * @param bool $expandedcontentEnablebottom
     * @return void
     */
    public function setExpandedcontentEnablebottom($expandedcontentEnablebottom): void
    {
        $this->expandedcontentEnablebottom = $expandedcontentEnablebottom;
    }

    /**
     * Returns the boolean state of expandedcontentEnablebottom
     *
     * @return bool
     */
    public function isExpandedcontentEnablebottom()
    {
        return $this->expandedcontentEnablebottom;
    }

    /**
     * Returns the expandedcontentSlidebottom
     *
     * @return bool $expandedcontentSlidebottom
     */
    public function getExpandedcontentSlidebottom()
    {
        return $this->expandedcontentSlidebottom;
    }

    /**
     * Sets the expandedcontentSlidebottom
     *
     * @param bool $expandedcontentSlidebottom
     * @return void
     */
    public function setExpandedcontentSlidebottom($expandedcontentSlidebottom): void
    {
        $this->expandedcontentSlidebottom = $expandedcontentSlidebottom;
    }

    /**
     * Returns the boolean state of expandedcontentSlidebottom
     *
     * @return bool
     */
    public function isExpandedcontentSlidebottom()
    {
        return $this->expandedcontentSlidebottom;
    }

    /**
     * Returns the expandedcontentContainerpositionbottom
     *
     * @return string $expandedcontentContainerpositionbottom
     */
    public function getExpandedcontentContainerpositionbottom()
    {
        return $this->expandedcontentContainerpositionbottom;
    }

    /**
     * Sets the expandedcontentContainerpositionbottom
     *
     * @param string $expandedcontentContainerpositionbottom
     * @return void
     */
    public function setExpandedcontentContainerpositionbottom($expandedcontentContainerpositionbottom): void
    {
        $this->expandedcontentContainerpositionbottom = $expandedcontentContainerpositionbottom;
    }

    /**
     * Returns the expandedcontentContainerbottom
     *
     * @return string $expandedcontentContainerbottom
     */
    public function getExpandedcontentContainerbottom()
    {
        return $this->expandedcontentContainerbottom;
    }

    /**
     * Sets the expandedcontentContainerbottom
     *
     * @param string $expandedcontentContainerbottom
     * @return void
     */
    public function setExpandedcontentContainerbottom($expandedcontentContainerbottom): void
    {
        $this->expandedcontentContainerbottom = $expandedcontentContainerbottom;
    }

    /**
     * Returns the expandedcontentClassbottom
     *
     * @return string $expandedcontentClassbottom
     */
    public function getExpandedcontentClassbottom()
    {
        return $this->expandedcontentClassbottom;
    }

    /**
     * Sets the expandedcontentClassbottom
     *
     * @param string $expandedcontentClassbottom
     * @return void
     */
    public function setExpandedcontentClassbottom($expandedcontentClassbottom): void
    {
        $this->expandedcontentClassbottom = $expandedcontentClassbottom;
    }


    /**
     * Returns the generalRootline
     *
     * @return bool $generalRootline
     */
    public function getGeneralRootline()
    {
        return $this->generalRootline;
    }

    /**
     * Sets the generalRootline
     *
     * @param bool $generalRootline
     * @return void
     */
    public function setGeneralRootline($generalRootline): void
    {
        $this->generalRootline = $generalRootline;
    }

    /**
     * Returns the boolean state of generalRootline
     *
     * @return bool
     */
    public function isGeneralRootline()
    {
        return $this->generalRootline;
    }

    /**
     * Returns the generalOverride
     *
     * @return bool $generalOverride
     */
    public function getGeneralOverride()
    {
        return $this->generalOverride;
    }

    /**
     * Sets the generalOverride
     *
     * @param bool $generalOverride
     * @return void
     */
    public function setGeneralOverride($generalOverride): void
    {
        $this->generalOverride = $generalOverride;
    }

    /**
     * Returns the boolean state of generalOverride
     *
     * @return bool
     */
    public function isGeneralOverride()
    {
        return $this->generalOverride;
    }

    /**
     * Returns the contentOnlyOnRootpage
     *
     * @return bool $contentOnlyOnRootpage
     */
    public function getContentOnlyOnRootpage()
    {
        return $this->contentOnlyOnRootpage;
    }

    /**
     * Sets the contentOnlyOnRootpage
     *
     * @param bool $contentOnlyOnRootpage
     * @return void
     */
    public function setContentOnlyOnRootpage($contentOnlyOnRootpage): void
    {
        $this->contentOnlyOnRootpage = $contentOnlyOnRootpage;
    }

    /**
     * Returns the compress
     *
     * @return bool $compress
     */
    public function getCompress()
    {
        return $this->compress;
    }

    /**
     * Sets the compress
     *
     * @param bool $compress
     * @return void
     */
    public function setCompress($compress): void
    {
        $this->compress = $compress;
    }


    /**
     * Returns the disablePrefixComment
     *
     * @return bool $disablePrefixComment
     */
    public function getDisablePrefixComment()
    {
        return $this->disablePrefixComment;
    }

    /**
     * Sets the disablePrefixComment
     *
     * @param bool $disablePrefixComment
     * @return void
     */
    public function setDisablePrefixComment($disablePrefixComment): void
    {
        $this->disablePrefixComment = $disablePrefixComment;
    }


    /**
     * Returns the containerError
     *
     * @return bool $containerError
     */
    public function getContainerError()
    {
        return $this->containerError;
    }

    /**
     * Sets the containerError
     *
     * @param bool $containerError
     * @return void
     */
    public function setContainerError($containerError): void
    {
        $this->containerError = $containerError;
    }


    /**
     * Returns the slideLeftAside
     *
     * @return bool $slideLeftAside
     */
    public function getSlideLeftAside()
    {
        return $this->slideLeftAside;
    }

    /**
     * Sets the $slideLeftAside
     *
     * @param bool $containerError
     * @return void
     */
    public function setSlideLeftAside($slideLeftAside): void
    {
        $this->slideLeftAside = $slideLeftAside;
    }

    /**
     * Returns the slideRightAside
     *
     * @return bool $slideRightAside
     */
    public function getSlideRightAside()
    {
        return $this->slideRightAside;
    }

    /**
     * Sets the slideRightAside
     *
     * @param bool $slideRightAside
     * @return void
     */
    public function setSlideRightAside($slideRightAside): void
    {
        $this->slideRightAside = $slideRightAside;
    }

    /**
     * Returns the submenuSticky
     *
     * @return bool $submenuSticky
     */
    public function getsubmenuSticky()
    {
        return $this->submenuSticky;
    }

    /**
     * Sets the submenuSticky
     *
     * @param bool $submenuSticky
     * @return void
     */
    public function setsubmenuSticky($submenuSticky): void
    {
        $this->submenuSticky = $submenuSticky;
    }

    /**
     * Returns the pageContentExtraClass
     *
     * @return string $pageContentExtraClass
     */
    public function getPageContentExtraClass()
    {
        return $this->pageContentExtraClass;
    }

    /**
     * Sets the pageContentExtraClass
     *
     * @param string $pageContentExtraClass
     * @return void
     */
    public function setPageContentExtraClass($pageContentExtraClass): void
    {
        $this->pageContentExtraClass = $pageContentExtraClass;
    }


    /**
     * Returns the bodyExtraClass
     *
     * @return string $bodyExtraClass
     */
    public function getBodyExtraClass()
    {
        return $this->bodyExtraClass;
    }

    /**
     * Sets the bodyExtraClass
     *
     * @param string $bodyExtraClass
     * @return void
     */
    public function setBodyExtraClass($bodyExtraClass): void
    {
        $this->bodyExtraClass = $bodyExtraClass;
    }

    /**
     * Returns the asideExtraClass
     *
     * @return string $asideExtraClass
     */
    public function getAsideExtraClass()
    {
        return $this->asideExtraClass;
    }

    /**
     * Sets the asideExtraClass
     *
     * @param string $asideExtraClass
     * @return void
     */
    public function setAsideExtraClass($asideExtraClass): void
    {
        $this->asideExtraClass = $asideExtraClass;
    }


    /**
     * Returns the mainExtraClass
     *
     * @return string $mainExtraClass
     */
    public function getMainExtraClass()
    {
        return $this->mainExtraClass;
    }

    /**
     * Sets the mainExtraClass
     *
     * @param string $mainExtraClass
     * @return void
     */
    public function setMainExtraClass($mainExtraClass): void
    {
        $this->mainExtraClass = $mainExtraClass;
    }

    /**
     * Returns the globalPaddingTop
     *
     * @return string $globalPaddingTop
     */
    public function getGlobalPaddingTop()
    {
        return $this->globalPaddingTop;
    }

    /**
     * Sets the globalPaddingTop
     *
     * @param string $globalPaddingTop
     * @return void
     */
    public function setGlobalPaddingTop($globalPaddingTop): void
    {
        $this->globalPaddingTop = $globalPaddingTop;
    }

    /**
     * Returns the stickyFooterExtraPadding
     *
     * @return int $stickyFooterExtraPadding
     */
    public function getStickyFooterExtraPadding()
    {
        return $this->stickyFooterExtraPadding;
    }

    /**
     * Sets the stickyFooterExtraPadding
     *
     * @param int $stickyFooterExtraPadding
     * @return void
     */
    public function setStickyFooterExtraPadding($stickyFooterExtraPadding): void
    {
        $this->stickyFooterExtraPadding = $stickyFooterExtraPadding;
    }

    /**
     * Returns the contentMarginTop
     *
     * @return string $contentMarginTop
     */
    public function getContentMarginTop()
    {
        return $this->contentMarginTop;
    }

    /**
     * Sets the contentMarginTop
     *
     * @param string $contentMarginTop
     * @return void
     */
    public function setContentMarginTop($contentMarginTop): void
    {
        $this->contentMarginTop = $contentMarginTop;
    }

    /**
     * Returns the loadingSpinner
     *
     * @return string $loadingSpinner
     */
    public function getLoadingSpinner()
    {
        return $this->loadingSpinner;
    }

    /**
     * Sets the loadingSpinner
     *
     * @param string $loadingSpinner
     * @return void
     */
    public function setLoadingSpinner($loadingSpinner): void
    {
        $this->loadingSpinner = $loadingSpinner;
    }

    /**
     * Returns the loadingSpinnerColor
     *
     * @return string $loadingSpinnerColor
     */
    public function getLoadingSpinnerColor()
    {
        return $this->loadingSpinnerColor;
    }

    /**
     * Sets the loadingSpinnerColor
     *
     * @param string $loadingSpinnerColor
     * @return void
     */
    public function setLoadingSpinnerColor($loadingSpinnerColor): void
    {
        $this->loadingSpinnerColor = $loadingSpinnerColor;
    }

    /**
     * Returns the lightboxSelection
     *
     * @return string $lightboxSelection
     */
    public function getLightboxSelection()
    {
        return $this->lightboxSelection;
    }

    /**
     * Sets the lightboxSelection
     *
     * @param string $lightboxSelection
     * @return void
     */
    public function setLightboxSelection($lightboxSelection): void
    {
        $this->lightboxSelection = $lightboxSelection;
    }

    /**
     * Returns the magnifying
     *
     * @return bool $magnifying
     */
    public function getMagnifying()
    {
        return $this->magnifying;
    }

    /**
     * Sets the magnifying
     *
     * @param bool $magnifying
     * @return void
     */
    public function setMagnifying($magnifying): void
    {
        $this->magnifying = $magnifying;
    }

    /**
     * Returns the sectionmenuAnchorOffset
     *
     * @return int $sectionmenuAnchorOffset
     */
    public function getSectionmenuAnchorOffset()
    {
        return $this->sectionmenuAnchorOffset;
    }

    /**
     * Sets the sectionmenuAnchorOffset
     *
     * @param int $sectionmenuAnchorOffset
     * @return void
     */
    public function setSectionmenuAnchorOffset($sectionmenuAnchorOffset): void
    {
        $this->sectionmenuAnchorOffset = $sectionmenuAnchorOffset;
    }

    /**
     * Returns the sectionmenuScrollspyThreshold
     *
     * @return string sectionmenuScrollspyThreshold
     */
    public function getSectionmenuScrollspyThreshold()
    {
        return $this->sectionmenuScrollspyThreshold;
    }

    /**
     * Sets the sectionmenuScrollspyThreshold
     *
     * @param string $sectionmenuScrollspyThreshold
     * @return void
     */
    public function setSectionmenuScrollspyThreshold($sectionmenuScrollspyThreshold): void
    {
        $this->sectionmenuScrollspyThreshold = $sectionmenuScrollspyThreshold;
    }

    /**
     * Returns the sectionmenuScrollspyRootMargin
     *
     * @return string sectionmenuScrollspyRootMargin
     */
    public function getSectionmenuScrollspyRootMargin()
    {
        return $this->sectionmenuScrollspyRootMargin;
    }

    /**
     * Sets the sectionmenuScrollspyRootMargin
     *
     * @param string $sectionmenuScrollspyRootMargin
     * @return void
     */
    public function setSectionmenuScrollspyRootMargin($sectionmenuScrollspyRootMargin): void
    {
        $this->sectionmenuScrollspyRootMargin = $sectionmenuScrollspyRootMargin;
    }

    /**
     * Returns the sectionmenuStickyTop
     *
     * @return bool $sectionmenuStickyTop
     */
    public function getSectionmenuStickyTop()
    {
        return $this->sectionmenuStickyTop;
    }

    /**
     * Sets the sectionmenuStickyTop
     *
     * @param bool $sectionmenuStickyTop
     * @return void
     */
    public function setSectionmenuStickyTop($sectionmenuStickyTop): void
    {
        $this->sectionmenuStickyTop = $sectionmenuStickyTop;
    }

    /**
     * Returns the sectionmenuScrollspy
     *
     * @return bool $sectionmenuScrollspy
     */
    public function getSectionmenuScrollspy()
    {
        return $this->sectionmenuScrollspy;
    }

    /**
     * Sets the sectionmenuScrollspy
     *
     * @param bool $sectionmenuScrollspy
     * @return void
     */
    public function setSectionmenuScrollspy($sectionmenuScrollspy): void
    {
        $this->sectionmenuScrollspy = $sectionmenuScrollspy;
    }

    /**
     * Returns the backgroundImageEnable
     *
     * @return bool $backgroundImageEnable
     */
    public function getBackgroundImageEnable()
    {
        return $this->backgroundImageEnable;
    }

    /**
     * Sets the backgroundImageEnable
     *
     * @param bool $backgroundImageEnable
     * @return void
     */
    public function setBackgroundImageEnable($backgroundImageEnable): void
    {
        $this->backgroundImageEnable = $backgroundImageEnable;
    }

    /**
     * Returns the backgroundImageSlide
     *
     * @return bool $backgroundImageSlide
     */
    public function getBackgroundImageSlide()
    {
        return $this->backgroundImageSlide;
    }

    /**
     * Sets the backgroundImageSlide
     *
     * @param bool $backgroundImageSlide
     * @return void
     */
    public function setBackgroundImageSlide($backgroundImageSlide): void
    {
        $this->backgroundImageSlide = $backgroundImageSlide;
    }

    /**
     * Returns the shrinkingNavPadding
     *
     * @return string $shrinkingNavPadding
     */
    public function getShrinkingNavPadding()
    {
        return $this->shrinkingNavPadding;
    }

    /**
     * Sets the shrinkingNavPadding
     *
     * @param string $shrinkingNavPadding
     * @return void
     */
    public function setShrinkingNavPadding($shrinkingNavPadding): void
    {
        $this->shrinkingNavPadding = $shrinkingNavPadding;
    }

    /**
     * Returns the sidebarMenuPosition
     *
     * @return string $sidebarMenuPosition
     */
    public function getSidebarMenuPosition()
    {
        return $this->sidebarMenuPosition;
    }

    /**
     * Sets the shrinkingNavPadding
     *
     * @param string $sidebarMenuPosition
     * @return void
     */
    public function setSidebarMenuPosition($sidebarMenuPosition): void
    {
        $this->sidebarMenuPosition = $sidebarMenuPosition;
    }

    /**
     * Returns the langMenuWithFaIcon
     *
     * @return bool $langMenuWithFaIcon
     */
    public function getLangMenuWithFaIcon()
    {
        return $this->langMenuWithFaIcon;
    }

    /**
     * Sets the langMenuWithFaIcon
     *
     * @param bool $langMenuWithFaIcon
     * @return void
     */
    public function setLangMenuWithFaIcon($langMenuWithFaIcon): void
    {
        $this->langMenuWithFaIcon = $langMenuWithFaIcon;
    }

    /**
     * Returns the subheaderColor
     *
     * @return string $subheaderColor
     */
    public function getSubheaderColor()
    {
        return $this->subheaderColor;
    }

    /**
     * Sets the subheaderColor
     *
     * @param string $subheaderColor
     * @return void
     */
    public function setSubheaderColor($subheaderColor): void
    {
        $this->subheaderColor = $subheaderColor;
    }

    /**
     * Returns the dateFormat
     *
     * @return string $dateFormat
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Sets the dateFormat
     *
     * @param string $dateFormat
     * @return void
     */
    public function setDateFormat($dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Returns the favicon
     *
     * @return string $favicon
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * Sets the favicon
     *
     * @param string $favicon
     * @return void
     */
    public function setFavicon($favicon): void
    {
        $this->favicon = $favicon;
    }

    /**
     * Returns cardFlipperOnClick
     *
     * @return bool $cardFlipperOnClick
     */
    public function getCardFlipperOnClick()
    {
        return $this->cardFlipperOnClick;
    }

    /**
     * Sets the cardFlipperOnClick
     *
     * @param bool $cardFlipperOnClick
     * @return void
     */
    public function setCardFlipperOnClick($cardFlipperOnClick): void
    {
        $this->cardFlipperOnClick = $cardFlipperOnClick;
    }

    /**
     * Returns lastModifiedContentElement
     *
     * @return bool $lastModifiedContentElement
     */
    public function getLastModifiedContentElement()
    {
        return $this->lastModifiedContentElement;
    }

    /**
     * Sets the lastModifiedContentElement
     *
     * @param bool $lastModifiedContentElement
     * @return void
     */
    public function setLastModifiedContentElement($lastModifiedContentElement): void
    {
        $this->lastModifiedContentElement = $lastModifiedContentElement;
    }

    /**
     * Returns recentlyUpdatedContentElements
     *
     * @return bool $recentlyUpdatedContentElements
     */
    public function getRecentlyUpdatedContentElements()
    {
        return $this->recentlyUpdatedContentElements;
    }

    /**
     * Sets the recentlyUpdatedContentElements
     *
     * @param bool $recentlyUpdatedContentElements
     * @return void
     */
    public function setRecentlyUpdatedContentElements($recentlyUpdatedContentElements): void
    {
        $this->recentlyUpdatedContentElements = $recentlyUpdatedContentElements;
    }

    /**
     * Returns updated
     *
     * @return int $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets the updated
     *
     * @param int $updated
     * @return void
     */
    public function setUpdated($updated): void
    {
        $this->updated = $updated;
    }

    /**
     * Returns gridupdated
     *
     * @return int $gridupdated
     */
    public function getGridupdated()
    {
        return $this->gridupdated;
    }

    /**
     * Sets the gridupdated
     *
     * @param int $gridupdated
     * @return void
     */
    public function setGridupdated($gridupdated): void
    {
        $this->gridupdated = $gridupdated;
    }

    /**
     * Returns the sidebarSectionMobile
     *
     * @return bool $sidebarSectionMobile
     */
    public function getSidebarSectionMobile()
    {
        return $this->sidebarSectionMobile;
    }

    /**
     * Sets the sidebarSectionMobile
     *
     * @param bool $sidebarSectionMobile
     * @return void
     */
    public function setSidebarSectionMobile($sidebarSectionMobile): void
    {
        $this->sidebarSectionMobile = $sidebarSectionMobile;
    }

    /**
     * Returns the sectionmenuIcons
     *
     * @return bool $sectionmenuIcons
     */
    public function getSectionmenuIcons()
    {
        return $this->sectionmenuIcons;
    }

    /**
     * Sets the sectionmenuIcons
     *
     * @param bool $sectionmenuIcons
     * @return void
     */
    public function setSectionmenuIcons($sectionmenuIcons): void
    {
        $this->sectionmenuIcons = $sectionmenuIcons;
    }



    /**
     * Returns the navbarDarkMode
     *
     * @return bool $navbarDarkMode
     */
    public function getNavbarDarkMode()
    {
        return $this->navbarDarkMode;
    }

    /**
     * Sets the navbarDarkMode
     *
     * @param bool $navbarDarkMode
     * @return void
     */
    public function setNavbarDarkMode($navbarDarkMode): void
    {
        $this->navbarDarkMode = $navbarDarkMode;
    }
}
