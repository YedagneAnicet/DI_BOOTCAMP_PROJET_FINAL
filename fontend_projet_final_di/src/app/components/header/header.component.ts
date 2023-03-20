import { GestionPannierService } from './../../services/gestion-pannier.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss'],
})
export class HeaderComponent implements OnInit {
  public totalItem: number = 0;

  constructor(private _pannier: GestionPannierService) {}
  ngOnInit(): void {
    this._pannier.getProducts().subscribe((response) => {
      this.totalItem = response.length;
    });
  }
}
