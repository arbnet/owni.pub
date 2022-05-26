<?php
// пространство имён ядра
namespace core;
// Класс Стэк
class Stack {
  private array $arob=[];
  // Получение объекта из стэка
  final public function Pop(){return array_pop($this->arob);}
  // Добавление объекта в стэк
  final public function Push(mixed $obj){array_push($this->arob,$obj);}
};
?>