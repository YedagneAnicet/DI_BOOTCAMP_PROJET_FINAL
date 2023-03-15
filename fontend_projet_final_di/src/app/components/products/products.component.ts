import { GestionPannierService } from './../../services/gestion-pannier.service';
import { ProduitsService } from './../../services/produits.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.scss'],
})
export class ProductsComponent {
  public productList: any;

  constructor(
    private _produuitService: ProduitsService,
    private _pannierService: GestionPannierService
  ) {}

  ngOnInit(): void {
    this._produuitService.getProduit().subscribe((response) => {
      this.productList = response;

      this.productList.forEach((a: any) => {
        Object.assign(a, { quantity: 1, total: a.price });
      });
    });
  }

  addtocart(item: any) {
    this._pannierService.addToCart(item);
  }
}
