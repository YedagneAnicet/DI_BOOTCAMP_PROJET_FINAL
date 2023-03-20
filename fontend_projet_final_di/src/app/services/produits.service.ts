import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class ProduitsService {
  private apiUrl = 'http://localhost:8080/api/produits';

  constructor(private http: HttpClient) {}

// ajouter un nouveau produit a liste de produit
  addProduct(product: any): Observable<any> {
    const url = `${this.apiUrl}/add`;
    return this.http.post(url, product);
  }


// obtenir la liste de tout les poroduits
  getAllProducts(): Observable<any> {
    const url = `${this.apiUrl}/all`;
    return this.http.get(url);
  }

// obtenir un produit spécifique avec son id
  getProductById(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get(url);
  }

// modifier un produit existant
  updateProduct(id: number, product: any): Observable<any> {
    const url = `${this.apiUrl}/update/${id}`;
    return this.http.put(url, product).pipe(
      catchError((error) => {
        return throwError(error.json().error || 'Server error');
      })
    );
  }


// supprimer un produit existant 
  deleteProduct(id: number): Observable<any> {
    const url = `${this.apiUrl}/delete/${id}`;
    return this.http
      .delete(url)
      .pipe(
        catchError((error: any) =>
          throwError(error.json().error || 'Server error')
        )
      );
  }

  
}
