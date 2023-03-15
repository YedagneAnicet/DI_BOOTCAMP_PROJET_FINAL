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

  addProduct(product: any): Observable<any> {
    const url = `${this.apiUrl}/add`;
    return this.http.post(url, product);
  }

  getAllProducts(): Observable<any> {
    const url = `${this.apiUrl}/all`;
    return this.http.get(url);
  }

  getProductById(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get(url);
  }

  updateProduct(id: number, product: any): Observable<any> {
    const url = `${this.apiUrl}/update/${id}`;
    return this.http.put(url, product).pipe(
      catchError((error) => {
        return throwError(error.json().error || 'Server error');
      })
    );
  }

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