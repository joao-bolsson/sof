<?php

/**
 * Interface that defines a HTML component.
 * 
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 Feb.
 */
interface Component {

    /**
     * Add a component inside this component.
     * @param Component $component Component to add.
     */
    function addComponent(Component $component);

    function __toString();
}
