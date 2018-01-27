<?php

/**
 * Interface that defines a HTML component.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 Feb.
 */
class Component {

    /**
     * Array with components inside.
     * @var array
     */
    protected $components;

    /**
     * @var string HTML tag name of this component.
     */
    protected $tag;

    /**
     * // TODO: substituir string por array aqui.
     * @var string CSS class of this component.
     */
    protected $class;

    /**
     * @var string Optional text to show in this component.
     */
    protected $text;

    /**
     * Component constructor.
     * @param string $tag Tag name of this component like 'button' or 'div'.
     * @param string $class CSS class of this component.
     * @param string $text The text to show in this component (optional).
     */
    public function __construct(string $tag, string $class, string $text = '') {
        $this->tag = $tag;
        $this->class = $class;

        $mb = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
        if ($mb == 'ISO-8859-1') {
            $text = utf8_encode($text);
        }

        $this->text = $text;
        $this->components = [];
    }

    /**
     * Add a component inside this component.
     * @param Component $component Component to add.
     */
    public function addComponent(Component $component) {
        $i = count($this->components);
        $this->components[$i] = $component;
    }

    /**
     * @return bool True if the body is empty, else - false.
     */
    public function isEmpty(): bool {
        return count($this->components) == 0;
    }

    /**
     * @return array Components.
     */
    public function getComponents(): array {
        return $this->components;
    }

    /**
     * @return string The string representation os this component.
     */
    public function __toString() {
        $component = '';
        if (!empty($this->tag)) {
            $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';

            $component = "<" . $this->tag . $class . ">" . $this->text;
        }

        foreach ($this->components as $comp) {
            $component .= $comp;
        }

        if (!empty($this->tag)) {
            $component .= "</" . $this->tag . ">";
        }
        return $component;
    }
}
