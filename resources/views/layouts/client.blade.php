<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tsena Vohitsoa ') }} - Tsena Vohitsoa</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">


<div id="app"></div>{{--
    La barre de navigation (menu Commandes + sous-menu, menu Autre,
    infos utilisateur, déconnexion) est désormais gérée entièrement
    par le composant Vue NavBar.vue, monté via App.vue.
    Voir : resources/js/App.vue, resources/js/components/NavBar.vue
--}}

</body>
</html>
