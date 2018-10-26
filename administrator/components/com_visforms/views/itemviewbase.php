<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         http://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 * @since        Joomla 3.6.2
 */

defined('_JEXEC') or die('Restricted access');

class VisFormsItemViewBase extends JViewLegacy
{
    // framework
    public $app;
    public $doc;
    public $input;
    public $user;
    public $userId;
    // component names
    public $baseName        = 'visforms';
    public $componentName   = 'com_visforms';
    public $editViewName;
    public $controllerName;
    public $baseUrl;
    // payload
    public $form;
    public $item;
    public $id;
    public $fid;
    public $canDo;
    public $canDoPostFix;
    public $isNew;
    public $checkedOut;
    public $cssName;

    function __construct($config = array())
    {
        parent::__construct($config);
        // framework
        $this->app          = JFactory::getApplication();
        $this->doc          = JFactory::getDocument();
        $this->input        = $this->app->input;
        $this->user		    = JFactory::getUser();
        $this->userId	    = $this->user->get('id');
        // component names
        $this->baseUrl      = "index.php?option=$this->componentName";
    }

    protected function initialize() {
        // payload
        $this->form		    = $this->get('Form');
        $this->item		    = $this->get('Item');
        $this->id           = (int) $this->item->id;
        $this->fid          = $this->getFIdFromInput();
        $this->canDo        = VisformsHelper::getActions($this->item->id);
        $this->canDoPostFix = '';
        $this->isNew	    = ($this->item->id == 0);
        $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->userId);
        // derived class specific member initialization
        $this->setMembers();
    }

    public function display($tpl = null) {
        $this->initialize();
        VisformsHelper::showTitleWithPreFix($this->getTitle());

        if ($this->isNew) {
            if ($this->canDo->get('core.create')) {
                JToolbarHelper::apply("$this->controllerName.apply");
                JToolbarHelper::save("$this->controllerName.save");
                JToolbarHelper::save2new("$this->controllerName.save2new");
            }
            JToolbarHelper::cancel("$this->controllerName.cancel");
        }
        else {
            // Can't save the record if it's checked out.
            if (!$this->checkedOut) {
                if ($this->canDo->get("core.edit$this->canDoPostFix") || ($this->canDo->get("core.edit.own$this->canDoPostFix") && $this->item->created_by == $this->userId)) {
                    JToolbarHelper::apply("$this->controllerName.apply");
                    JToolbarHelper::save("$this->controllerName.save");
                    $this->setToolbarNotCheckedOut();
                }
            }
            $this->setToolbar();
            JToolbarHelper::cancel("$this->controllerName.cancel", 'COM_VISFORMS_CLOSE');
        }

        $this->addHeaderDeclarations();
        VisformsHelper::addCommonViewStyleCss();

        JFactory::getApplication()->input->set('hidemainmenu', 1);

        parent::display($tpl);
    }

    // overwrites: template methods

    protected function setMembers() { }

    protected function getTitle() { }

    protected function setToolbar() { }

    protected function addHeaderDeclarations() { }

    // overwrites: internal

    protected function getFIdUrlQueryName() {
        return 'fid';
    }

    protected function setToolbarNotCheckedOut() {
        if ($this->canDo->get('core.create')) {
            JToolbarHelper::save2new("$this->controllerName.save2new");
        }
    }

    // implementation

    private function getFIdFromInput() {
        $name = $this->getFIdUrlQueryName();
        return $this->input->getInt($name, -1);
    }
}