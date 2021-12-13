<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SliderCategory
 *
 * @ORM\Table(name="slider_category", indexes={@ORM\Index(name="fk_slider_category_category_idx", columns={"category_id"}), @ORM\Index(name="fk_slider_category_slider_idx", columns={"slider_id"})})
 * @ORM\Entity
 */
class SliderCategory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var \Slider
     *
     * @ORM\ManyToOne(targetEntity="Slider")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="slider_id", referencedColumnName="id")
     * })
     */
    private $slider;


}
