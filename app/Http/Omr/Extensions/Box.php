<?php

namespace Encore\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Box extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.box';

    /**
     * @var string
     */
    protected $title = 'Box header';

    /**
     * @var string
     */
    protected $content = 'here is the box content.';

    /**
     * @var BoxTools
     */
    protected $tools;


    /**
     * Box constructor.
     *
     * @param string $title
     * @param string $content
     * @param BoxTools $tools
     */
    public function __construct($title = '', $content = '', BoxTools $tools = null)
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        $this->class('box');

        $this->tools = $tools ;
    }

    /**
     * Set box content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof Renderable) {
            $this->content = $content->render();
        } else {
            $this->content = (string) $content;
        }

        return $this;
    }

    /**
     * Set box title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

  /**
   * Set box tools.
   *
   * @param BoxTools $tools
   *
   * @return $this
   */
  public function setTools(BoxTools $tools)
  {
    $this->tools = $tools;

    return $this;
  }

  /**
   * @return collection|string
   */
  public function getTools()
  {
    return $this->tools ? $this->tools->getTools() : [];
  }

  /**
     * Set box as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->tools->append(
            '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>');

        return $this;
    }

    /**
     * Set box as removable.
     *
     * @return $this
     */
    public function removable()
    {
        $this->tools->append('<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>');

        return $this;
    }

    /**
     * Set box style.
     *
     * @param string $styles
     *
     * @return $this|Box
     */
    public function style($styles)
    {
        if (is_string($styles)) {
            return $this->style([$styles]);
        }

        $styles = array_map(function ($style) {
            return 'box-'.$style;
        }, $styles);

        $this->class = $this->class.' '.implode(' ', $styles);

        return $this;
    }

    /**
     * Add `box-solid` class to box.
     *
     * @return $this
     */
    public function solid()
    {
        return $this->style('solid');
    }

    /**
     * Variables in view.
     *
     * @return array
     */
    protected function variables()
    {
        return [
            'title'         => $this->title,
            'content'       => $this->content,
            'tools'         => $this->getTools(),
            'attributes'    => $this->formatAttributes(),
        ];
    }

    /**
     * Render box.
     *
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->variables())->render();
    }
}