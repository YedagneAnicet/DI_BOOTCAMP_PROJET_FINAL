import { Component, Input } from '@angular/core';
import { GestionPannierService } from 'src/app/services/gestion-pannier.service';
import { ProduitsService } from 'src/app/services/produits.service';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.scss'],
})
export class ProductsComponent {
  public productList: any;
  @Input() search: string = '';

  quantity: number = 1;

  constructor(
    private _produitService: ProduitsService,
    private _pannier: GestionPannierService
  ) {}

  ngOnInit(): void {
    this._produitService.getAllProducts().subscribe((response) => {
      this.productList = response;
      this.productList.forEach((a: any) => {
        Object.assign(a, {
          quantity: this.quantity,
          total: a.prix_produit * this.quantity,
        });
      });
    });
  }

  addtocart(item: any) {
    this._pannier.addToCart(item);
  }





}
