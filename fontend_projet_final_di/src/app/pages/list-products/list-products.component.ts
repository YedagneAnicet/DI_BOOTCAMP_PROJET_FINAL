import { ProduitsService } from 'src/app/services/produits.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-list-products',
  templateUrl: './list-products.component.html',
  styleUrls: ['./list-products.component.scss'],
})
export class ListProductsComponent implements OnInit {
  listeCategory!: any;


  constructor(private _produitService: ProduitsService) {}

  ngOnInit() {
    this.getCategory();
  }

  getCategory() {
    this._produitService.getAllCategory().subscribe({
      next: (reponse: any) => {
        this.listeCategory = reponse;
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }


}
