import { Routes } from '@angular/router';
import { Home } from './components/home/home';
import { Cliente } from './components/cliente/cliente';
import { Page404 } from './components/page404/page404';

export const routes: Routes = [
    {path: '', redirectTo: 'home', pathMatch: 'full' },
    {path: 'home', component: Home },
    {path: 'cliente', component: Cliente},

    {path: '**', component: Page404}

];
