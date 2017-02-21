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

    public function __construct(string $type = 'button', string $class = '', string $onclick = '', string $attr = '', string $title = '', string $icon = '') {
        $this->type = $type;
        $this->class = $class;
        $this->onclick = $onclick;
        $this->attr = $attr;
        $this->title = $title;
        $this->icon = $icon;
    }

    public function __toString(): string {
        $class = $onclick = $title = $attr = $icon = '';

        if (!empty($this->class)) {
            $class = " class=\"" . $this->class . "\"";
        }

        if (!empty($this->onclick)) {
            $onclick = " onclick=\"" . $this->onclick . "\"";
        }

        if (!empty($this->title)) {
            $title = " title=\"" . $this->title . "\"";
        }

        if (!empty($this->attr)) {
            $attr = " " . $this->attr;
        }

        if (!empty($this->icon)) {
            $icon = "<i class=\"fa fa-" . $this->icon . "\"></i>";
        }

        $button = "<button type=\"" . $this->type . "\"" . $class . $onclick . $attr . $title . ">" . $icon . "</button>";

        return $button;
    }

}
