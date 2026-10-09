@switch($slug)
  @case('coxs-bazar')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22c0-6 1-10 3-13"/><path d="M15 9c-1-3-4-4-7-3 2 0 4 1 5 3M15 9c2-2 5-2 7 0-3-1-5 0-6 1M15 9c0-3 1-5 3-6-1 2-1 4-1 6"/></svg>
    @break
  @case('sundarbans')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21v-6M12 15c-4 0-6-3-5-6 1-3 4-4 5-6 1 2 4 3 5 6 1 3-1 6-5 6z"/></svg>
    @break
  @case('sylhet')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15M5 19l8-8"/></svg>
    @break
  @case('bandarban')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 19l6-10 4 6 3-4 5 8z"/></svg>
    @break
  @case('saint-martin')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21 4 11a8 8 0 0 1 16 0z"/><path d="M12 21V8M8 19l-3-8M16 19l3-8"/></svg>
    @break
  @case('kuakata')
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 18h18M7 18a5 5 0 0 1 10 0M12 6v3M5 10l2 2M19 10l-2 2"/></svg>
    @break
  @default
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-6.3-7-11a7 7 0 0 1 14 0c0 4.700-7 11-7 11z"/><circle cx="12" cy="10" r="2.500"/></svg>
@endswitch
