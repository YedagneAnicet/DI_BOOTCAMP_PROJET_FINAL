import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import {HttpClientModule, HTTP_INTERCEPTORS} from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './pages/home/home.component';
import { HeaderComponent } from './components/header/header.component';
import { BlogComponent } from './components/blog/blog.component';
import { FooterComponent } from './components/footer/footer.component';
import { ProductsComponent } from './components/products/products.component';
import { LoginComponent } from './components/login/login.component';
import { ListProductsComponent } from './pages/list-products/list-products.component';
import { ListArticleBlogComponent } from './pages/list-article-blog/list-article-blog.component';
import { ReactiveFormsModule } from '@angular/forms';
import { PannierComponent } from './pages/pannier/pannier.component';
import { ListePharmacieComponent } from './pages/liste-pharmacie/liste-pharmacie.component';
import { RegisterComponent } from './components/register/register.component';
import { CorsInterceptor } from './cors.interceptor';



@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    HeaderComponent,
    BlogComponent,
    FooterComponent,
    ProductsComponent,
    LoginComponent,
    ListProductsComponent,
    ListArticleBlogComponent,
    PannierComponent,
    ListePharmacieComponent,
    RegisterComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ReactiveFormsModule,
    HttpClientModule
  ],
  providers: [{provide: HTTP_INTERCEPTORS, useClass: CorsInterceptor, multi: true}],
  bootstrap: [AppComponent]
})
export class AppModule { }
