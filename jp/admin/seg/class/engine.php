<?php

interface engine {

  function init($keyword,$countLimit=10);
  function parseResult($html);
  function preSearch();
  function getCurrentPageResult();
  //  function search($keyword);
  //  function getPageCount($keyword);

}
