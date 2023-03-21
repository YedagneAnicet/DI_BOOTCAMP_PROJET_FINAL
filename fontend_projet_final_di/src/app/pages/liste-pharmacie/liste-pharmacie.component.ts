import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { PharmacieService } from 'src/app/services/pharmacie.service';

@Component({
  selector: 'app-liste-pharmacie',
  templateUrl: './liste-pharmacie.component.html',
  styleUrls: ['./liste-pharmacie.component.scss'],
})
export class ListePharmacieComponent implements OnInit {
  listeCommune!: any;

  ListePharmacieGarde!: any;

  ListePharmacieCommune!: any;

  FormRecherche!: FormGroup;

  allgarde = true;

  constructor(
    private _pharmacieService: PharmacieService,
    private _fb: FormBuilder
  ) {}

  ngOnInit(): void {
    this.getCommune();
    this.getPharmacieGarde();
    this.FormRecherche = this._fb.group({
      commune: [''],
    });
  }

  getCommune() {
    this._pharmacieService.getAllCommune().subscribe({
      next: (reponse: any) => {
        this.listeCommune = reponse;
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }

  getPharmacieGarde() {
    this._pharmacieService.getGardePharmacies().subscribe({
      next: (reponse: any) => {
        this.ListePharmacieGarde = reponse;
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }

  // obtenir la liste de toutes les pharmacie de garde par commune
  onSubmit() {
    if (this.FormRecherche.invalid) {
      return;
    }
    this.allgarde = false;
    const commune = this.FormRecherche.controls['commune'].value;

    console.log(commune);

    this._pharmacieService.getPharmaciesByVilleAndGarde(commune).subscribe({
      next: (reponse: any) => {
        this.ListePharmacieCommune = reponse;
      },
      error: (error: any) => {
        console.log(error);
      },
    });
  }
}
