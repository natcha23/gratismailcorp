@if($paginator->getLastPage() >= 10)
  <?php $previousPage = ($paginator->getCurrentPage() > 1) ? $paginator->getCurrentPage() - 1 : 1; ?>  
  <ul class="pagination">
    <li><a href="{{ $paginator->getUrl($paginator->getPerPage()) }}" class="{{ ($paginator->getCurrentPage() == 1) ? ' disabled' : '' }}">First</a></li>
    <li><a href="{{ $paginator->getUrl($previousPage) }}" class="{{ ($paginator->getCurrentPage() == 1) ? ' disabled' : '' }}">Previous</a></li>
    @for ($i = 1; $i <= $paginator->getLastPage(); $i++)
      <li class="item{{ ($paginator->getCurrentPage() == $i) ? ' active disabled' : '' }}"><a href="{{ $paginator->getUrl($i) }}">{{ $i }}</a></li>
    @endfor
    <li><a href="{{ $paginator->getUrl($paginator->getCurrentPage()+1) }}">Next</a></li>
    <li><a href="{{ $paginator->getUrl($paginator->getLastPage()) }}" class="{{ ($paginator->getCurrentPage() == 1) ? ' disabled' : '' }}">Last</a></li>
  </ul>
@elseif ($paginator->getLastPage() > 1)
<?php $previousPage = ($paginator->getCurrentPage() > 1) ? $paginator->getCurrentPage() - 1 : 1; ?>  
  <ul class="pagination">
    <li><a href="{{ $paginator->getUrl($previousPage) }}" class="{{ ($paginator->getCurrentPage() == 1) ? ' disabled' : '' }}">Previous</a></li>
    @for ($i = 1; $i <= $paginator->getLastPage(); $i++)
      <li class="item{{ ($paginator->getCurrentPage() == $i) ? ' active disabled' : '' }}"><a href="{{ $paginator->getUrl($i) }}">{{ $i }}</a></li>
    @endfor
    <li><a href="{{ $paginator->getUrl($paginator->getCurrentPage()+1) }}">Next</a></li>
  </ul> 
@endif