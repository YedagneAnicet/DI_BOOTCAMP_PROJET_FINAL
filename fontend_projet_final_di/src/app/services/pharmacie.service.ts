import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class PharmacieService {
  private apiUrl = 'http://localhost:8080/api/pharmacies';

  constructor(private http: HttpClient) {}

  addPharmacy(pharmacy: any): Observable<any> {
    const url = `${this.apiUrl}/add`;
    return this.http.post(url, pharmacy);
  }

  getAllPharmacies(): Observable<any> {
    const url = `${this.apiUrl}/all`;
    return this.http.get(url);
  }

  getPharmacyById(id: number): Observable<any> {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get(url);
  }

  getGardePharmacies(): Observable<any> {
    const url = `${this.apiUrl}/all/garde`;
    return this.http.get(url);
  }

  updatePharmacy(id: number, pharmacy: any): Observable<any> {
    const url = `${this.apiUrl}/update/${id}`;
    return this.http
      .put(url, pharmacy)
      .pipe(
        catchError(error => {
          return throwError(error.json().error  || 'Server error');
        })
      );
  }

  deletePharmacy(id: number): Observable<any> {
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
