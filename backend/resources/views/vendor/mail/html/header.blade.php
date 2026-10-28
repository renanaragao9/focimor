@props(['url'])
<tr>
<td class="header" bgcolor="#ec4899" style="background-color: #ec4899; background-image: linear-gradient(135deg, #ec4899 0%, #60a5fa 100%);">
<a href="{{ $url }}" style="display: inline-block;">
<span class="paws">🐾</span> {!! $slot !!} <span class="paws">🦴</span>
</a>
<div class="tagline">Focinho + amor: conectando pets, famílias e ONGs</div>
</td>
</tr>
