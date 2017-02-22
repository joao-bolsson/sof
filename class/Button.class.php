<?php

/**
 * Button's HTML representation.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 21 Feb.
 */
class Button {

    /**
     * Type of button, default: button
     * @var type String
     */
    private $type;

    /**
     * Class css of this button.
     * @var type String
     */
    private $class;

    /**
     * Method onclick of this button
     * @var type String
     */
    private $onclick;

    /**
     * Others attributes of <button></button>
     * @var type String
     */
    private $attr;

    /**
     * Title of this button.
     * @var type String
     */
    private $title;

    /**
     * Button's icon.
     * @var type String
     */
    private $icon;

    /**
     * Button's HTML representation.
     * 
     * @param string $type Button's type.
     * @param string $class Button's class.
     * @param string $onclick Method onclick of this button.
     * @param string $attr Other attributes for this button.
     * @param string $title Button's title
     * @param string $icon Button's icon.
     */
    public function __construct(string $type = 'button', string $class = '', string $onclick = '', string $attr = '', string $title = '', string $icon = '') {
        $this->type = $type;
        $this->class = $class;
        $this->onclick = $onclick;
        $this->attr = $attr;
        $this->title = $title;
        $this->icon = $icon;
    }

    public function __toString(): string {
        $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';

        $onclick = (!empty($this->onclick)) ? " onclick=\"" . $this->onclick . "\"" : '';

        $title = (!empty($this->title)) ? " title=\"" . $this->title . "\"" : '';

        $attr = (!empty($this->attr)) ? " " . $this->attr : '';

        $icon = (!empty($this->icon)) ? "<i class=\"fa fa-" . $this->icon . "\"></i>" : '';

        $this->type = (empty($this->type)) ? 'button' : $this->type;

        $button = "<button type=\"" . $this->type . "\"" . $class . $onclick . $attr . $title . ">" . $icon . "</button>";

        return $button;
    }

}
