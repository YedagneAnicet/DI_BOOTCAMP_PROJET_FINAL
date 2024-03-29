import { ListArticleBlogComponent } from './pages/list-article-blog/list-article-blog.component';
import { HomeComponent } from './pages/home/home.component';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { ListProductsComponent } from './pages/list-products/list-products.component';
import { PannierComponent } from './pages/pannier/pannier.component';
import { ListePharmacieComponent } from './pages/liste-pharmacie/liste-pharmacie.component';
import { RegisterComponent } from './components/register/register.component';

const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' },
  { path: 'home', component: HomeComponent },
  { path: 'liste', component: ListProductsComponent },
  { path: 'blog', component: ListArticleBlogComponent },
  { path: 'login', component: LoginComponent },
  { path: 'pannier', component: PannierComponent },
  { path: 'garde', component: ListePharmacieComponent },
  { path: 'register', component: RegisterComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
