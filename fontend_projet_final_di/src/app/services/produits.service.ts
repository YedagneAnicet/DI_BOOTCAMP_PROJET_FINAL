import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ProduitsService {
  constructor(private _http: HttpClient) {}

  getProduit() {
    return this._http.get<any>('').pipe(
      map((response: any) => {
        return response;
      })
    );
  }

}
