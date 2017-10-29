<?php

/**
 * Button's HTML representation.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 21 Feb.
 */
final class Button extends Component {

    /**
     * Type of button, default: button
     * @var string
     */
    private $type;

    /**
     * Method onclick of this button
     * @var string
     */
    private $onclick;

    /**
     * Others attributes of <button></button>
     * @var string
     */
    private $attr;

    /**
     * Title of this button.
     * @var string
     */
    private $title;

    /**
     * Button's icon.
     * @var string
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
        parent::__construct('button', $class);
        $this->type = $type;
        $this->onclick = $onclick;
        $this->attr = $attr;
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * @return string The string representation of this button.
     */
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

    public function addComponent(Component $c) {
        // do nothing
    }

}
