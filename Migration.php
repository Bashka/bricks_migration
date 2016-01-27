<?php
namespace Bricks\Migration;

interface Migration{
  public function up();

  public function down();
}
