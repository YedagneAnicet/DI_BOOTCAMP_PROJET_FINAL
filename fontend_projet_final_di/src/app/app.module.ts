import { LOCALE_ID, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { registerLocaleData } from '@angular/common';
import localeFr from '@angular/common/locales/fr';

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
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { PannierComponent } from './pages/pannier/pannier.component';
import { ListePharmacieComponent } from './pages/liste-pharmacie/liste-pharmacie.component';
import { RegisterComponent } from './components/register/register.component';
import { CorsInterceptor } from './cors.interceptor';
import { AuthInterceptor } from './auth.interceptor';
import { ProductsHomeComponent } from './components/products-home/products-home.component';
import { MapComponent } from './components/map/map.component';

registerLocaleData(localeFr, 'fr');
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
    RegisterComponent,
    ProductsHomeComponent,
    MapComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ReactiveFormsModule,
    HttpClientModule,
    FormsModule,
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: CorsInterceptor, multi: true },
    { provide: LOCALE_ID, useValue: 'fr' },
    { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true },
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
