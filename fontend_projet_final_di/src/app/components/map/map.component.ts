import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-map',
  templateUrl: './map.component.html',
  styleUrls: ['./map.component.scss'],
})
export class MapComponent implements OnInit {
  map !: google.maps.Map;
  @Input() location!: string;

  constructor() {}

  ngOnInit() {
    this.initMap();
    this.addMarker(this.location);
  }

  initMap(): void {
    const mapElement = document.getElementById('map');
    if (mapElement) {
      this.map = new google.maps.Map(mapElement, {
        center: { lat: 0, lng: 0 },
        zoom: 8,
      });
    } else {
      console.log('Element with ID "map" not found');
    }
  }

  addMarker(location: string): void {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: location }, (results, status) => {
      if (status === 'OK') {
        this.map.setCenter(results[0].geometry.location);
        new google.maps.Marker({
          map: this.map,
          position: results[0].geometry.location,
        });
      } else {
        console.log(
          'Geocode was not successful for the following reason: ' + status
        );
      }
    });
  }
}
