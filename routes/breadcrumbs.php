<?php

// use Illuminate\Support\Facades\Route;
// Home
Breadcrumbs::for('agent-list', function ($trail) {
    $trail->push('List agent', route('agent.list'));
});
Breadcrumbs::for('agent-manager', function ($trail, $manager) {
    $trail->parent('agent-list');
    $trail->push($manager->name, route('agent.manager-staff', $manager->id));
});
Breadcrumbs::for('agent-status', function ($trail, $user) {
    $trail->parent('agent-list');
    $trail->push($user->name. '-'.'list agent inactive', route('agent.list-status-noactive', $user->id));
});
